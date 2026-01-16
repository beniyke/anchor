<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Product Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Helpers\String\Str;
use Money\Money;
use Wave\Models\Product;

class ProductBuilder
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

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function price(int|float $price): self
    {
        $this->data['price'] = $price;

        return $this;
    }

    public function currency(string $currency): self
    {
        $this->data['currency'] = $currency;

        return $this;
    }

    public function active(): self
    {
        $this->data['status'] = 'active';

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->data['metadata'] = $metadata;

        return $this;
    }

    public function create(): Product
    {
        // Handle currency conversion
        $currency = $this->data['currency'] ?? 'USD';

        if (isset($this->data['price'])) {
            $money = is_float($this->data['price'])
                ? Money::amount($this->data['price'], $currency)
                : Money::make($this->data['price'], $currency);

            $this->data['price'] = (int) $money->getAmount();
        }

        $payload = array_merge($this->data, [
            'refid' => Str::random('alnum', 16),
            'currency' => $currency,
            'status' => $this->data['status'] ?? 'active',
        ]);

        return Product::create($payload);
    }
}
