<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invoice Manager for handling billing and payments.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Core\Services\ConfigServiceInterface;
use Database\DB;
use Exception;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Mail\Mail;
use Money\Money;
use Pay\Enums\Status as PayStatus;
use Pay\Pay;
use Wallet\Services\WalletManagerService;
use Wave\Enums\InvoiceStatus;
use Wave\Models\Discount;
use Wave\Models\Invoice;
use Wave\Models\InvoiceItem;
use Wave\Models\Subscription;
use Wave\Models\TaxRate;
use Wave\Notifications\InvoiceGeneratedNotification;
use Wave\Notifications\PaymentFailedNotification;
use Wave\Notifications\PaymentSuccessNotification;
use Wave\Services\Builders\InvoiceBuilder;

class InvoiceManagerService
{
    public function __construct(
        private readonly WalletManagerService $walletManager,
        private readonly CouponManagerService $couponManager,
        private readonly AffiliateManagerService $affiliateManager,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function make(): InvoiceBuilder
    {
        return new InvoiceBuilder($this);
    }

    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create(array_merge($data, [
                'refid' => Str::random('alnum', 16),
                'status' => $data['status'] ?? InvoiceStatus::OPEN,
                'tax' => 0,
                'total' => $data['amount'], // Initial total before tax
                'invoice_number' => $this->generateInvoiceNumber(),
                'due_at' => $data['due_at'] ?? DateTimeHelper::now(),
                'metadata' => $data['metadata'] ?? [],
            ]));

            if (!empty($data['description'])) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $data['description'],
                    'amount' => $data['amount'],
                    'quantity' => 1,
                    'type' => 'one-time',
                    'metadata' => $data['metadata'] ?? [],
                ]);
            }

            if (!empty($data['coupon_code'])) {
                $coupon = $this->couponManager->findByCode($data['coupon_code']);
                if ($coupon && $coupon->isValid()) {
                    $savings = $this->couponManager->calculateSavings($coupon, $invoice->amount);

                    Discount::create([
                        'owner_id' => $invoice->owner_id,
                        'owner_type' => $invoice->owner_type,
                        'invoice_id' => $invoice->id, // One-time invoices might not have subscription_id
                        'coupon_id' => $coupon->id,
                        'amount_saved' => $savings,
                    ]);

                    $coupon->update(['times_redeemed' => $coupon->times_redeemed + 1]);

                    $invoice->update([
                        'amount' => max(0, $invoice->amount - $savings),
                        'total' => max(0, $invoice->total - $savings),
                    ]);
                }
            }

            $this->calculateTax($invoice);

            $this->sendInvoiceNotification($invoice);

            return $invoice;
        });
    }

    public function find(string|int $id): ?Invoice
    {
        return Invoice::find($id);
    }

    public function createFromSubscription(Subscription $subscription, ?string $description = null, array $metadata = []): Invoice
    {
        return DB::transaction(function () use ($subscription, $description, $metadata) {
            $plan = $subscription->plan;
            $amount = $plan->price * $subscription->quantity;

            $prorationCredit = $metadata['proration_credit'] ?? 0;
            $amount = max(0, $amount - $prorationCredit);

            $invoice = Invoice::create([
                'refid' => Str::random('alnum', 16),
                'owner_id' => $subscription->owner_id,
                'owner_type' => $subscription->owner_type,
                'subscription_id' => $subscription->id,
                'status' => InvoiceStatus::OPEN,
                'amount' => $amount,
                'tax' => 0,
                'total' => $amount,
                'currency' => $plan->currency,
                'invoice_number' => $this->generateInvoiceNumber(),
                'due_at' => DateTimeHelper::now(),
                'metadata' => array_merge($subscription->metadata ?? [], $metadata),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $description ?? "Subscription to {$plan->name}",
                'amount' => $amount,
                'quantity' => $subscription->quantity,
                'type' => 'subscription',
                'metadata' => $metadata,
            ]);

            $this->couponManager->applyRecurringDiscounts($subscription, $invoice);

            $this->calculateTax($invoice);

            $this->attemptPayment($invoice);

            $this->sendInvoiceNotification($invoice);

            return $invoice;
        });
    }

    public function calculateTax(Invoice $invoice): void
    {
        if (!$this->config->get('wave.tax.enabled', true)) {
            return;
        }

        $country = $invoice->metadata['billing_country'] ?? $this->config->get('wave.tax.default_country');
        $state = $invoice->metadata['billing_state'] ?? null;

        $query = TaxRate::query();
        if ($country) {
            $query->where('country', $country);
        }
        if ($state) {
            $query->where('state', $state);
        }

        $taxRate = $query->first();

        $rate = $taxRate ? $taxRate->rate : (float) $this->config->get('wave.tax.rate', 0);
        $isInclusive = $taxRate ? $taxRate->is_inclusive : (bool) $this->config->get('wave.tax.inclusive', false);

        if ($isInclusive) {
            $baseAmount = (int) ($invoice->amount / (1 + ($rate / 100)));
            $taxAmount = $invoice->amount - $baseAmount;

            $invoice->update([
                'tax' => $taxAmount,
                'total' => $invoice->amount,
            ]);
        } else {
            $taxAmount = (int) ($invoice->amount * ($rate / 100));

            $invoice->update([
                'tax' => $taxAmount,
                'total' => $invoice->amount + $taxAmount,
            ]);
        }
    }

    public function attemptPayment(Invoice $invoice): bool
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            return true;
        }

        if ($this->attemptWalletPayment($invoice)) {
            return true;
        }

        if ($this->attemptPayPayment($invoice)) {
            return true;
        }

        $invoice->update(['status' => InvoiceStatus::OPEN]);

        $this->sendPaymentFailedNotification($invoice);

        return false;
    }

    public function markAsPaid(Invoice $invoice, string $driver = 'system', ?string $transactionId = null): void
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            return;
        }

        DB::transaction(function () use ($invoice, $driver, $transactionId) {
            $invoice->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => DateTimeHelper::now(),
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'paid_via' => $driver,
                    'pay_transaction_id' => $transactionId
                ])
            ]);

            if ($invoice->subscription_id && $invoice->subscription) {
                $subscription = $invoice->subscription;
                $plan = $subscription->plan;

                $subManager = app(SubscriptionManagerService::class);
                $newStart = $subscription->current_period_end;
                $newEnd = $subManager->calculatePeriodEnd($newStart, $plan);

                $subscription->update([
                    'current_period_start' => $newStart,
                    'current_period_end' => $newEnd,
                    'status' => 'active', // Ensure status is active if it was past_due
                ]);

                $this->affiliateManager->onConversion($subscription);
            }
        });

        $this->sendPaymentSuccessNotification($invoice);
    }

    private function attemptWalletPayment(Invoice $invoice): bool
    {
        try {
            $wallet = $this->walletManager->findByOwner(
                $invoice->owner_id,
                $invoice->owner_type,
                $invoice->currency
            );

            if (!$wallet) {
                return false;
            }

            $amount = Money::make($invoice->total, $invoice->currency);
            $balance = $this->walletManager->getBalance($wallet->id);

            if ($balance->lessThan($amount)) {
                return false;
            }

            $this->walletManager->debit($wallet->id, $amount, [
                'description' => "Payment for Invoice #{$invoice->invoice_number}",
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_refid' => $invoice->refid
                ]
            ]);

            $invoice->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => DateTimeHelper::now(),
            ]);

            $this->sendPaymentSuccessNotification($invoice);

            return true;
        } catch (Exception $e) {
            logger('wave.log')->error("Wallet payment failed for Invoice #{$invoice->id}: " . $e->getMessage());

            return false;
        }
    }

    private function attemptPayPayment(Invoice $invoice): bool
    {
        if (! empty($invoice->metadata['checkout_url'])) {
            return true;
        }

        try {
            $strategy = $this->config->get('wave.payment_strategy', 'direct');
            $action = ($strategy === 'wallet') ? 'fund_wallet' : 'pay_invoice';

            $payment = Pay::initialize([
                'amount' => $invoice->total,
                'email' => $invoice->metadata['email'] ?? '',
                'reference' => $invoice->invoice_number,
                'currency' => $invoice->currency,
                'metadata' => array_merge($invoice->metadata ?? [], [
                    'invoice_id' => $invoice->id,
                    'wave_payment' => true,
                    'action' => $action
                ])
            ]);

            if ($payment->status === PayStatus::SUCCESS) {
                $this->sendPaymentSuccessNotification($invoice);

                return true;
            } elseif ($payment->authorizationUrl) {
                $invoice->update([
                    'metadata' => array_merge($invoice->metadata ?? [], [
                        'checkout_url' => $payment->authorizationUrl,
                        'provider_reference' => $payment->providerReference ?? null
                    ])
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            logger('wave.log')->error("Pay initialization failed for Invoice #{$invoice->id}: " . $e->getMessage());

            return false;
        }
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = $this->config->get('wave.invoice.prefix', 'INV-');
        $year = date('Y');

        $count = Invoice::query()
            ->where('invoice_number', 'LIKE', "{$prefix}{$year}-%")
            ->count();

        $sequence = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$year}-{$sequence}";
    }

    private function sendInvoiceNotification(Invoice $invoice): void
    {
        $email = $invoice->metadata['email'] ?? null;

        if ($email) {
            $payload = Data::make([
                'email' => $email,
                'name' => $invoice->metadata['name'] ?? '',
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->currency . ' ' . number_format($invoice->total / 100, 2),
                'due_date' => $invoice->due_at->format('Y-m-d'),
                'view_url' => $this->config->get('wave.invoice.url', '/billing/invoices') . '/' . $invoice->refid,
            ]);

            Mail::send(new InvoiceGeneratedNotification($payload));
        }
    }

    private function sendPaymentSuccessNotification(Invoice $invoice): void
    {
        $email = $invoice->metadata['email'] ?? null;

        if ($email) {
            $payload = Data::make([
                'email' => $email,
                'name' => $invoice->metadata['name'] ?? '',
                'invoice_number' => $invoice->invoice_number,
                'view_url' => $this->config->get('wave.invoice.url', '/billing/invoices') . '/' . $invoice->refid,
            ]);

            Mail::send(new PaymentSuccessNotification($payload));
        }
    }

    private function sendPaymentFailedNotification(Invoice $invoice): void
    {
        $email = $invoice->metadata['email'] ?? null;

        if ($email) {
            $payload = Data::make([
                'email' => $email,
                'name' => $invoice->metadata['name'] ?? '',
                'invoice_number' => $invoice->invoice_number,
                'update_url' => $this->config->get('wave.invoice.payment_method_url', '/billing/payment-method'),
            ]);

            Mail::send(new PaymentFailedNotification($payload));
        }
    }
}
