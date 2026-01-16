<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when currencies do not match.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Exceptions;

class CurrencyMismatchException extends MoneyException
{
    public static function create(string $currency1, string $currency2): self
    {
        return new self("Cannot perform operation on different currencies: {$currency1} and {$currency2}");
    }
}
