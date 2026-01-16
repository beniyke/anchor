<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Process Payment Success
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Listeners;

use Core\Contracts\ShouldQueue;
use Exception;
use Money\Money;
use Pay\Events\PaymentSuccessfulEvent;
use Wave\Enums\InvoiceStatus;
use Wave\Wave;

class ProcessPaymentSuccessListener implements ShouldQueue
{
    public function handle(PaymentSuccessfulEvent $event): void
    {
        $transaction = $event->transaction;
        $metadata = $transaction->metadata ?? [];

        // Only process Wave payments
        if (! ($metadata['wave_payment'] ?? false)) {
            return;
        }

        $invoiceId = $metadata['invoice_id'] ?? null;
        if (! $invoiceId) {
            return;
        }

        $invoice = Wave::invoices()->find($invoiceId);
        if (! $invoice) {
            logger('wave.log')->error("ProcessPaymentSuccessListener: Invoice #{$invoiceId} not found.");

            return;
        }

        // Prevent double processing
        if ($invoice->status === InvoiceStatus::PAID) {
            return;
        }

        $action = $metadata['action'] ?? 'pay_invoice';

        try {
            if ($action === 'fund_wallet') {
                $this->handleWalletFunding($invoice, $transaction);
            } else {
                $this->handleDirectPayment($invoice, $transaction);
            }
        } catch (Exception $e) {
            logger('wave.log')->error("ProcessPaymentSuccessListener Error: " . $e->getMessage());
        }
    }

    private function handleWalletFunding($invoice, $transaction): void
    {
        $amount = Money::make($transaction->amount, $transaction->currency);

        // We credit the wallet associated with the invoice owner
        // Assuming wallet exists (it should if we chose wallet funding)
        $wallet = Wave::wallet()->findByOwner($invoice->owner_id, $invoice->owner_type, $invoice->currency);

        if (! $wallet) {
            // Auto-create wallet if missing? Or log error?
            $wallet = Wave::wallet()->create($invoice->owner_id, $invoice->owner_type, $invoice->currency);
        }

        Wave::wallet()->credit($wallet->id, $amount, [
            'description' => "Wallet Funding via Pay (Tx: {$transaction->reference})",
            'payment_processor' => $transaction->driver,
            'processor_transaction_id' => $transaction->provider_reference,
            'metadata' => ['pay_transaction_id' => $transaction->id]
        ]);

        $paid = Wave::invoices()->attemptPayment($invoice);

        if (! $paid) {
            logger('wave.log')->error("ProcessPaymentSuccessListener: Wallet funding successful, but subsequent wallet payment failed for Invoice #{$invoice->id}.");
        }
    }

    private function handleDirectPayment($invoice, $transaction): void
    {
        Wave::invoices()->markAsPaid(
            $invoice,
            'pay_direct',
            (string) $transaction->id
        );
    }
}
