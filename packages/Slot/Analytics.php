<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Slot Analytics Facade
 */

namespace Slot;

use Slot\Services\SlotAnalyticsService;

class Analytics
{
    public static function __callStatic(string $method, array $arguments)
    {
        return resolve(SlotAnalyticsService::class)->$method(...$arguments);
    }
}
