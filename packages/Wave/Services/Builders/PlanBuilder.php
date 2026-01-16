<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Plan Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Money\Money;
use Wave\Models\Plan;

class PlanBuilder
{
    private array $data = [];

    public function __construct()
    {
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->data['slug'] = $slug;

        return $this;
    }

    public function price(int|float $amount): self
    {
        $currency = $this->data['currency'] ?? 'USD';

        if (is_float($amount)) {
            $money = Money::amount($amount, $currency);
        } else {
            $money = Money::make($amount, $currency);
        }

        $this->data['price'] = (int) $money->getAmount();

        return $this;
    }

    public function currency(string $currency): self
    {
        $this->data['currency'] = $currency;

        return $this;
    }

    public function interval(string $interval): self
    {
        $this->data['interval'] = $interval;

        return $this;
    }

    // Fluent helpers for interval
    public function daily(): self
    {
        return $this->interval('day');
    }

    public function weekly(): self
    {
        return $this->interval('week');
    }

    public function monthly(): self
    {
        return $this->interval('month');
    }

    public function yearly(): self
    {
        return $this->interval('year');
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function trialDays(int $days): self
    {
        $this->data['trial_days'] = $days;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->data['status'] = $active ? 'active' : 'inactive';

        return $this;
    }

    public function save(): Plan
    {
        return Plan::create($this->data);
    }
}
