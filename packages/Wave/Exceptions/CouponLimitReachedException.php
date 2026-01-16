<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when a coupon's usage limit has been reached.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class CouponLimitReachedException extends CouponException
{
    public function __construct(string $code)
    {
        parent::__construct("The coupon '{$code}' has reached its usage limit.");
    }
}
