<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Coupon Duration Enum
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Enums;

enum CouponDuration: string
{
    case ONCE = 'once';
    case REPEATING = 'repeating';
    case FOREVER = 'forever';
}
