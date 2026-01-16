<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fee Calculator Service
 *
 * Calculates transaction fees based on configurable rules
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services;

use Database\DB;
use Money\Money;
use Wallet\Enums\TransactionType;

class FeeCalculatorService
{
    private string|TransactionType $type = TransactionType::CREDIT;

    private ?string $processor = null;

    public function credit(int|float|Money $amount, ?string $currency = null): array
    {
        $this->type = TransactionType::CREDIT;
        $money = $amount instanceof Money ? $amount : Money::amount($amount, $currency ?? 'USD');

        return $this->calculate($this->type, $money, $this->processor);
    }

    public function debit(int|float|Money $amount, ?string $currency = null): array
    {
        $this->type = TransactionType::DEBIT;
        $money = $amount instanceof Money ? $amount : Money::amount($amount, $currency ?? 'USD');

        return $this->calculate($this->type, $money, $this->processor);
    }

    public function forProcessor(string $processor): self
    {
        $this->processor = $processor;

        return $this;
    }

    public function calculate(string|TransactionType $type, Money $amount, ?string $processor = null): array
    {
        $rule = $this->findApplicableRule($type, $amount, $processor);

        if (! $rule) {
            return [
                'fee' => Money::make(0, $amount->getCurrency()),
                'net_amount' => $amount,
                'rule_id' => null,
            ];
        }

        $feeAmount = $this->calculateFeeAmount($amount, $rule);

        // Apply min/max constraints
        if ($rule->min_fee > 0 && $feeAmount->getAmount() < $rule->min_fee) {
            $feeAmount = Money::make($rule->min_fee, $amount->getCurrency());
        }

        if ($rule->max_fee !== null && $feeAmount->getAmount() > $rule->max_fee) {
            $feeAmount = Money::make($rule->max_fee, $amount->getCurrency());
        }

        return [
            'fee' => $feeAmount,
            'net_amount' => $amount->subtract($feeAmount),
            'rule_id' => $rule->id,
        ];
    }

    private function findApplicableRule(string|TransactionType $type, Money $amount, ?string $processor): ?object
    {
        $typeValue = $type instanceof TransactionType ? $type->value : $type;
        $query = DB::table('wallet_fee_rule')
            ->where('transaction_type', $typeValue)
            ->where('currency', (string) $amount->getCurrency())
            ->where('is_active', true);

        // Filter by processor
        if ($processor) {
            $query->where(function ($q) use ($processor) {
                $q->where('payment_processor', $processor)
                    ->orWhereNull('payment_processor');
            });
        } else {
            $query->whereNull('payment_processor');
        }

        // Filter by amount range
        $query->where(function ($q) use ($amount) {
            $q->where(function ($q2) use ($amount) {
                $q2->whereNull('min_transaction_amount')
                    ->orWhere('min_transaction_amount', '<=', $amount->getAmount());
            })->where(function ($q2) use ($amount) {
                $q2->whereNull('max_transaction_amount')
                    ->orWhere('max_transaction_amount', '>=', $amount->getAmount());
            });
        });

        // Get most specific rule (with processor preferred)
        return $query->orderBy('payment_processor', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Calculate fee amount based on rule type
     */
    private function calculateFeeAmount(Money $amount, object $rule): Money
    {
        return match ($rule->fee_type) {
            'FIXED' => Money::make($rule->fixed_amount, $amount->getCurrency()),
            'PERCENTAGE' => $amount->multiply($rule->percentage),
            'TIERED' => $this->calculateTieredFee($amount, $rule),
            default => Money::make(0, $amount->getCurrency()),
        };
    }

    private function calculateTieredFee(Money $amount, object $rule): Money
    {
        // Simple tiered: fixed + percentage
        $fixed = Money::make($rule->fixed_amount, $amount->getCurrency());
        $percentage = $amount->multiply($rule->percentage);

        return $fixed->add($percentage);
    }
}
