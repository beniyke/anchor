<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Coupon Model for managing discounts.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Wave\Enums\CouponDuration;
use Wave\Enums\CouponType;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $code
 * @property string          $name
 * @property CouponType      $type
 * @property int             $value
 * @property string          $currency
 * @property CouponDuration  $duration
 * @property ?int            $duration_in_months
 * @property ?int            $max_redemptions
 * @property int             $times_redeemed
 * @property ?DateTimeHelper $expires_at
 * @property string          $status
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 *
 * @method static Builder isActive()
 * @method static Builder isExpired()
 */
class Coupon extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_coupon';

    protected array $fillable = [
        'refid',
        'code',
        'name',
        'type',
        'value',
        'currency',
        'duration',
        'duration_in_months',
        'max_redemptions',
        'times_redeemed',
        'expires_at',
        'status'
    ];

    protected array $casts = [
        'id' => 'integer',
        'type' => CouponType::class,
        'value' => 'integer',
        'duration' => CouponDuration::class,
        'duration_in_months' => 'integer',
        'max_redemptions' => 'integer',
        'times_redeemed' => 'integer',
        'expires_at' => 'datetime'
    ];

    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && DateTimeHelper::parse($this->expires_at)->isPast()) {
            return false;
        }

        if ($this->max_redemptions && $this->times_redeemed >= $this->max_redemptions) {
            return false;
        }

        return true;
    }

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeIsExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', DateTimeHelper::now());
    }
}
