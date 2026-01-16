<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Currency value object.
 * Represents an ISO 4217 currency with its properties.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

use Money\Exceptions\InvalidCurrencyException;

class Currency
{
    private function __construct(
        private readonly string $code,
        private readonly int $numericCode,
        private readonly int $minorUnit,
        private readonly string $name,
        private readonly string $symbol
    ) {
    }

    /**
     * Create currency from ISO code
     */
    public static function of(string $code): self
    {
        $code = strtoupper($code);
        $data = CurrencyRepository::find($code);

        if (! $data) {
            throw InvalidCurrencyException::unknownCurrency($code);
        }

        return new self(
            code: $code,
            numericCode: $data['numeric_code'],
            minorUnit: $data['minor_unit'],
            name: $data['name'],
            symbol: $data['symbol']
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    /**
     * Get number of decimal places
     */
    public function getMinorUnit(): int
    {
        return $this->minorUnit;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * Get subunit (e.g., 100 for USD cents)
     */
    public function getSubunit(): int
    {
        return (int) pow(10, $this->minorUnit);
    }

    public function equals(Currency $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * Check if currency code matches
     */
    public function is(string $code): bool
    {
        return $this->code === strtoupper($code);
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
