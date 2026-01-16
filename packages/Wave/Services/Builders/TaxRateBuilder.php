<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tax Rate Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services\Builders;

use Wave\Models\TaxRate;

class TaxRateBuilder
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

    public function rate(float $percentage): self
    {
        $this->data['rate'] = $percentage;

        return $this;
    }

    public function country(string $countryCode): self
    {
        $this->data['country'] = $countryCode;

        return $this;
    }

    public function state(string $stateCode): self
    {
        $this->data['state'] = $stateCode;

        return $this;
    }

    public function inclusive(): self
    {
        $this->data['is_inclusive'] = true;

        return $this;
    }

    public function exclusive(): self
    {
        $this->data['is_inclusive'] = false;

        return $this;
    }

    public function create(): TaxRate
    {
        $this->data['is_inclusive'] = $this->data['is_inclusive'] ?? false;

        return TaxRate::create($this->data);
    }
}
