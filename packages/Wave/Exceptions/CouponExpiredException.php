<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exception thrown when a coupon has expired.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Exceptions;

class CouponExpiredException extends CouponException
{
    public function __construct(string $code)
    {
        parent::__construct("The coupon '{$code}' has expired.");
    }
}
