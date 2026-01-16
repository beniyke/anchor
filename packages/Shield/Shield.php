<?php

declare(strict_types=1);

namespace Shield;

use Shield\Drivers\CaptchaDriverInterface;
use Shield\Services\ShieldAnalytics;
use Shield\Services\ShieldManagerService;

/**
 * Shield Static Facade.
 */
class Shield
{
    /**
     * Verify the token using the default driver.
     */
    public static function verify(string $token, ?string $ip = null): bool
    {
        return resolve(ShieldManagerService::class)->verify($token, $ip);
    }

    public static function render(?string $driver = null, array $attributes = []): string
    {
        return resolve(ShieldManagerService::class)->render($driver, $attributes);
    }

    public static function getScript(?string $driver = null): string
    {
        return resolve(ShieldManagerService::class)->getScript($driver);
    }

    public static function driver(?string $driver = null): CaptchaDriverInterface
    {
        return resolve(ShieldManagerService::class)->driver($driver);
    }

    public static function analytics(): ShieldAnalytics
    {
        return resolve(ShieldManagerService::class)->analytics();
    }
}
