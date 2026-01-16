<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when an invalid currency is provided.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Exceptions;

class InvalidCurrencyException extends MoneyException
{
    public static function unknownCurrency(string $code): self
    {
        return new self("Unknown currency code: {$code}");
    }
}
