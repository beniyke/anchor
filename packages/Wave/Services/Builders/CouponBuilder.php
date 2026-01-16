<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Coupon Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Helpers\String\Str;
use Money\Money;
use Wave\Enums\CouponDuration;
use Wave\Enums\CouponType;
use Wave\Models\Coupon;

class CouponBuilder
{
    private array $data = [];

    public function __construct()
    {
    }

    public function code(string $code): self
    {
        $this->data['code'] = $code;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function percent(int|float $percentage): self
    {
        $this->data['type'] = CouponType::PERCENT->value;
        // Store as basis points (e.g., 20.00% -> 2000)
        $this->data['value'] = (int) ($percentage * 100);

        return $this;
    }

    public function fixed(int|float $amount, string $currency = 'USD'): self
    {
        $this->data['type'] = CouponType::FIXED->value;
        $this->data['currency'] = $currency;

        $money = is_float($amount) ? Money::amount($amount, $currency) : Money::make($amount, $currency);
        $this->data['value'] = (int) $money->getAmount();

        return $this;
    }

    public function once(): self
    {
        $this->data['duration'] = CouponDuration::ONCE->value;

        return $this;
    }

    public function forever(): self
    {
        $this->data['duration'] = CouponDuration::FOREVER->value;

        return $this;
    }

    public function repeating(int $months): self
    {
        $this->data['duration'] = CouponDuration::REPEATING->value;
        $this->data['duration_in_months'] = $months;

        return $this;
    }

    public function maxRedemptions(int $max): self
    {
        $this->data['max_redemptions'] = $max;

        return $this;
    }

    public function expires($date): self
    {
        $this->data['expires_at'] = $date;

        return $this;
    }

    public function create(): Coupon
    {
        $payload = array_merge($this->data, [
            'refid' => Str::random('alnum', 16),
            'status' => 'active',
        ]);

        if (!isset($payload['name']) && isset($payload['code'])) {
            $payload['name'] = $payload['code'];
        }

        return Coupon::create($payload);
    }
}
