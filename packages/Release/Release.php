<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release;

use Release\Services\ReleaseAnalyticsService;
use Release\Services\ReleaseManagerService;

/**
 * @method static array|null              check()
 * @method static bool                    update()
 * @method static ReleaseAnalyticsService analytics()
 *
 * @see ReleaseManagerService
 */
class Release
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ReleaseManagerService::class;
    }

    public static function __callStatic($method, $args)
    {
        $instance = resolve(static::getFacadeAccessor());

        return $instance->$method(...$args);
    }
}
