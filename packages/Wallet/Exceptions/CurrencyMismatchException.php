<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Currency Mismatch Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use BackedEnum;
use Exception;
use UnitEnum;

class CurrencyMismatchException extends Exception
{
    public function __construct(string|UnitEnum $expected, string|UnitEnum $actual)
    {
        $expected = $expected instanceof BackedEnum ? (string) $expected->value : (string) $expected;
        $actual = $actual instanceof BackedEnum ? (string) $actual->value : (string) $actual;
        parent::__construct("Currency mismatch. Expected: {$expected}, Got: {$actual}", 400);
    }
}
