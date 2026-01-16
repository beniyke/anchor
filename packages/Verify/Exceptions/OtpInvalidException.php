<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Invalid Exception
 *
 * Thrown when OTP code doesn't match stored value
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Exceptions;

use Exception;

class OtpInvalidException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("Invalid OTP code for '{$identifier}'");
    }
}
