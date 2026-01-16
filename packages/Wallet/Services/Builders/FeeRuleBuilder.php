<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fee Rule Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use Money\Money;
use Wallet\Enums\Currency;
use Wallet\Enums\FeeType;
use Wallet\Enums\TransactionType;
use Wallet\Models\FeeRule;

class FeeRuleBuilder
{
    private array $attributes = [
        'fixed_amount' => 0,
        'percentage' => 0,
        'min_fee' => 0,
        'max_fee' => 3000000, // $30,000 default max
        'currency' => 'USD',
        'is_active' => true,
    ];

    public function __construct(string $name)
    {
        $this->attributes['name'] = $name;
    }

    public function forType(TransactionType|string $type): self
    {
        $this->attributes['transaction_type'] = $type instanceof TransactionType ? $type->value : $type;

        return $this;
    }

    public function credit(): self
    {
        return $this->forType(TransactionType::CREDIT);
    }

    public function debit(): self
    {
        return $this->forType(TransactionType::DEBIT);
    }

    public function fixed(int|float $amount): self
    {
        $this->attributes['fee_type'] = FeeType::FIXED->value;
        $this->attributes['fixed_amount'] = Money::amount($amount, $this->attributes['currency'])->getAmount();

        return $this;
    }

    public function percentage(float $percentage): self
    {
        $this->attributes['fee_type'] = FeeType::PERCENTAGE->value;
        // Convert to decimal (e.g. 10 -> 0.10)
        $this->attributes['percentage'] = $percentage / 100;

        return $this;
    }

    public function tiered(int|float $fixed, float $percentage): self
    {
        $this->attributes['fee_type'] = FeeType::TIERED->value;
        $this->attributes['fixed_amount'] = Money::amount($fixed, $this->attributes['currency'])->getAmount();
        $this->attributes['percentage'] = $percentage / 100;

        return $this;
    }

    public function currency(Currency|string $currency): self
    {
        $this->attributes['currency'] = $currency instanceof Currency ? $currency->value : $currency;

        return $this;
    }

    public function minFee(int|float $amount): self
    {
        $this->attributes['min_fee'] = Money::amount($amount, $this->attributes['currency'])->getAmount();

        return $this;
    }

    public function maxFee(int|float $amount): self
    {
        $this->attributes['max_fee'] = Money::amount($amount, $this->attributes['currency'])->getAmount();

        return $this;
    }

    public function minAmount(int|float $amount): self
    {
        $this->attributes['min_transaction_amount'] = Money::amount($amount, $this->attributes['currency'])->getAmount();

        return $this;
    }

    public function maxAmount(int|float $amount): self
    {
        $this->attributes['max_transaction_amount'] = Money::amount($amount, $this->attributes['currency'])->getAmount();

        return $this;
    }

    public function inactive(): self
    {
        $this->attributes['is_active'] = false;

        return $this;
    }

    public function save(): FeeRule
    {
        return FeeRule::create($this->attributes);
    }
}
