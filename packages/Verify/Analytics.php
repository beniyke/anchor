<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Verify Analytics Facade
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify;

use Verify\Services\VerifyAnalyticsService;

class Analytics
{
    public static function __callStatic(string $method, array $arguments)
    {
        return resolve(VerifyAnalyticsService::class)->$method(...$arguments);
    }
}
