<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Not Found Exception
 *
 * Thrown when OTP code doesn't exist for the given identifier
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Exceptions;

use Exception;

class OtpNotFoundException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("No OTP code found for '{$identifier}'");
    }
}
