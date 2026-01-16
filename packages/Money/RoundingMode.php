<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rounding mode constants.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Money;

class RoundingMode
{
    public const HALF_UP = PHP_ROUND_HALF_UP;     // 1
    public const HALF_DOWN = PHP_ROUND_HALF_DOWN; // 2
    public const HALF_EVEN = PHP_ROUND_HALF_EVEN; // 3
    public const CEILING = 4;  // Round towards positive infinity
    public const FLOOR = 5;    // Round towards negative infinity
    public const DOWN = 6;     // Round towards zero
    public const UP = 7;       // Round away from zero
}
