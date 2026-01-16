<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when an exchange rate is not found.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Exceptions;

class ExchangeRateNotFoundException extends MoneyException
{
    public static function forCurrencies(string $from, string $to): self
    {
        return new self("Exchange rate not found for {$from} to {$to}");
    }
}
