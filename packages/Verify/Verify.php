<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Provides convenient static methods for OTP verification.
 * It serves as a static proxy to the VerifyManagerService service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify;

use Verify\Services\OtpBuilderService;
use Verify\Services\VerifyAnalyticsService;
use Verify\Services\VerifyManagerService;

/**
 * @method static VerifyAnalyticsService analytics()
 */
class Verify
{
    public static function generate(string $identifier, string $channel = 'email'): string
    {
        return resolve(VerifyManagerService::class)->generate($identifier, $channel);
    }

    public static function verify(string $identifier, string $code): bool
    {
        return resolve(VerifyManagerService::class)->verify($identifier, $code);
    }

    public static function send(string $identifier, string $channel = 'email', ?string $receiverName = null): bool
    {
        return resolve(VerifyManagerService::class)->send($identifier, $channel, $receiverName);
    }

    public static function resend(string $identifier, string $channel = 'email', ?string $receiverName = null): bool
    {
        return resolve(VerifyManagerService::class)->resend($identifier, $channel, $receiverName);
    }

    public static function delete(string $identifier): bool
    {
        return resolve(VerifyManagerService::class)->delete($identifier);
    }

    public static function hasPending(string $identifier): bool
    {
        return resolve(VerifyManagerService::class)->hasPending($identifier);
    }

    public static function otp(string $identifier): OtpBuilderService
    {
        return resolve(VerifyManagerService::class)->otp($identifier);
    }

    public static function analytics(): VerifyAnalyticsService
    {
        return resolve(VerifyAnalyticsService::class);
    }
}
