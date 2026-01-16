<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Expired Exception
 *
 * Thrown when attempting to verify an expired OTP code
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Exceptions;

use Exception;

class OtpExpiredException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("OTP code for '{$identifier}' has expired");
    }
}
