<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait for models that require a date key for time-based partitioning.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Models\Traits;

use Helpers\DateTimeHelper;

trait HasDateKey
{
    /**
     * Boot the trait to add automatic date key generation.
     */
    public static function bootHasDateKey(): void
    {
        static::saving(function ($model) {
            if (! isset($model->attributes['date_key'])) {
                $now = isset($model->attributes['created_at'])
                    ? DateTimeHelper::parse($model->attributes['created_at'])
                    : DateTimeHelper::now();

                $model->attributes['date_key'] = $now->format('Y-m-d');
            }
        });
    }
}
