<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use Wallet\Enums\Currency;
use Wallet\Models\Wallet;

class WalletBuilder
{
    private array $attributes = [
        'currency' => 'USD',
        'balance' => 0,
    ];

    public function owner(int|string $ownerId, string $ownerType): self
    {
        $this->attributes['owner_id'] = $ownerId;
        $this->attributes['owner_type'] = $ownerType;

        return $this;
    }

    public function currency(string|Currency $currency): self
    {
        $this->attributes['currency'] = $currency instanceof Currency ? $currency->value : strtoupper($currency);

        return $this;
    }

    public function balance(int $balance): self
    {
        $this->attributes['balance'] = $balance;

        return $this;
    }

    public function create(): Wallet
    {
        return Wallet::create($this->attributes);
    }
}
