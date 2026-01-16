<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Coupon Type Enum
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Enums;

enum CouponType: string
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';
}
