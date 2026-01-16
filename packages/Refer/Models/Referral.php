<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Referral model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;
use Refer\Enums\ReferralStatus;

/**
 * @property int             $id
 * @property int             $code_id
 * @property int             $referrer_id
 * @property int             $referee_id
 * @property ReferralStatus  $status
 * @property int             $referrer_reward
 * @property int             $referee_reward
 * @property ?DateTimeHelper $rewarded_at
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ReferralCode $code
 * @property-read User $referrer
 * @property-read User $referee
 *
 * @method static Builder pending()
 * @method static Builder forReferrer(int $userId)
 */
class Referral extends BaseModel
{
    protected string $table = 'referral';

    protected array $fillable = [
        'code_id',
        'referrer_id',
        'referee_id',
        'status',
        'referrer_reward',
        'referee_reward',
        'rewarded_at',
        'metadata',
    ];

    protected array $casts = [
        'code_id' => 'int',
        'referrer_id' => 'int',
        'referee_id' => 'int',
        'status' => ReferralStatus::class,
        'referrer_reward' => 'int',
        'referee_reward' => 'int',
        'rewarded_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function code(): BelongsTo
    {
        return $this->belongsTo(ReferralCode::class, 'code_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function isPending(): bool
    {
        return $this->status === ReferralStatus::PENDING;
    }

    public function isRewarded(): bool
    {
        return $this->status === ReferralStatus::REWARDED;
    }

    public function markAsRewarded(): void
    {
        $this->update([
            'status' => ReferralStatus::REWARDED,
            'rewarded_at' => DateTimeHelper::now(),
        ]);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::PENDING);
    }

    public function scopeForReferrer(Builder $query, int $userId): Builder
    {
        return $query->where('referrer_id', $userId);
    }
}
