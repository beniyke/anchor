<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Generator Interface
 *
 * Defines the contract for generating OTP codes
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Contracts;

interface OtpGeneratorInterface
{
    /**
     * Generate a random OTP code
     *
     * @param int $length Length of the code (4-8 digits)
     *
     * @return string Generated OTP code
     */
    public function generate(int $length): string;
}
