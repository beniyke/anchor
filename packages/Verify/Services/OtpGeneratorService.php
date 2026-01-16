<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Generator Service
 *
 * Generates cryptographically secure random OTP codes
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Helpers\String\Str;
use Verify\Contracts\OtpGeneratorInterface;

class OtpGeneratorService implements OtpGeneratorInterface
{
    public function generate(int $length): string
    {
        if ($length < 4 || $length > 8) {
            $length = 8;
        }

        return Str::random('numeric', $length);
    }
}
