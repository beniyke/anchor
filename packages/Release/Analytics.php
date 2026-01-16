<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Analytics Facade
 */

namespace Release;

use Release\Services\ReleaseAnalyticsService;

class Analytics
{
    public static function __callStatic(string $method, array $arguments)
    {
        return resolve(ReleaseAnalyticsService::class)->$method(...$arguments);
    }
}
