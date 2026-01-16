<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Plan Model for subscription plans.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Traits\HasRefid;
use Wave\Enums\PlanInterval;

/**
 * @property int          $id
 * @property string       $refid
 * @property string       $name
 * @property string       $slug
 * @property ?string      $description
 * @property int          $price
 * @property string       $currency
 * @property PlanInterval $interval
 * @property int          $interval_count
 * @property int          $trial_days
 * @property string       $status
 * @property ?array       $metadata
 *
 * @method static Builder isActive()
 * @method static Builder isInactive()
 * @method static Builder isMonthly()
 * @method static Builder isQuarterly()
 * @method static Builder isBiannual()
 * @method static Builder isYearly()
 * @method static Builder isDaily()
 * @method static Builder isWeekly()
 */
class Plan extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_plan';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'interval',
        'interval_count',
        'trial_days',
        'status',
        'metadata'
    ];

    protected array $casts = [
        'id' => 'integer',
        'price' => 'integer',
        'interval' => PlanInterval::class,
        'interval_count' => 'integer',
        'trial_days' => 'integer',
        'metadata' => 'json'
    ];

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeIsInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function scopeIsMonthly(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::MONTH->value)
            ->where('interval_count', 1);
    }

    public function scopeIsQuarterly(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::MONTH->value)
            ->where('interval_count', 3);
    }

    public function scopeIsBiannual(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::MONTH->value)
            ->where('interval_count', 6);
    }

    public function scopeIsYearly(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::YEAR->value)
            ->where('interval_count', 1);
    }

    public function scopeIsDaily(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::DAY->value);
    }

    public function scopeIsWeekly(Builder $query): Builder
    {
        return $query->where('interval', PlanInterval::WEEK->value);
    }
}
