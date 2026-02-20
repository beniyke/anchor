<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait to clear notification cache when notifications are modified.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Models\Traits;

use Helpers\File\Cache;

trait ClearsNotificationCache
{
    /**
     * Boot the trait to add automatic cache clearing.
     */
    public static function bootClearsNotificationCache(): void
    {
        $clearCache = function ($notification) {
            Cache::create('query')->flushTags(['notifications', "user:{$notification->user_id}"]);
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
