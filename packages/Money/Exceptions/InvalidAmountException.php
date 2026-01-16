<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when an invalid amount is provided.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money\Exceptions;

class InvalidAmountException extends MoneyException
{
    public static function divisionByZero(): self
    {
        return new self('Cannot divide money by zero');
    }

    public static function invalidAmount(mixed $amount): self
    {
        $type = get_debug_type($amount);

        return new self("Invalid money amount: expected int or numeric string, got {$type}");
    }
}
