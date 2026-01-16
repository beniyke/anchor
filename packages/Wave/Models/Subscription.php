<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Subscription Model for tracking active billings.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Wave\Enums\SubscriptionStatus;

/**
 * @property int                       $id
 * @property string                    $refid
 * @property int                       $owner_id
 * @property string                    $owner_type
 * @property int                       $plan_id
 * @property string|SubscriptionStatus $status
 * @property int                       $quantity
 * @property ?DateTimeHelper           $trial_ends_at
 * @property ?DateTimeHelper           $current_period_start
 * @property ?DateTimeHelper           $current_period_end
 * @property ?DateTimeHelper           $ends_at
 * @property ?DateTimeHelper           $canceled_at
 * @property ?DateTimeHelper           $created_at
 * @property ?DateTimeHelper           $updated_at
 * @property ?array                    $metadata
 * @property-read Plan $plan
 * @property-read ModelCollection $invoices
 *
 * @method static Builder active()
 * @method static Builder trialing()
 * @method static Builder canceled()
 * @method static Builder pastDue()
 * @method static Builder owner(int $ownerId)
 */
class Subscription extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_subscription';

    protected array $fillable = [
        'refid',
        'owner_id',
        'owner_type',
        'plan_id',
        'status',
        'quantity',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'ends_at',
        'canceled_at',
        'metadata'
    ];

    protected array $casts = [
        'id' => 'integer',
        'status' => SubscriptionStatus::class,
        'quantity' => 'integer',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'metadata' => 'json'
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::ACTIVE);
    }

    public function scopeTrialing(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::TRIALING);
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::CANCELED);
    }

    public function scopePastDue(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::PAST_DUE);
    }

    public function scopeOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeIsRenewableInDays(Builder $query, int $days): Builder
    {
        return $query->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('current_period_end', '<=', DateTimeHelper::now()->addDays($days));
    }

    public function isCurrentlyActive(): bool
    {
        if (in_array($this->status, [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::TRIALING->value])) {
            return true;
        }

        if ($this->status === SubscriptionStatus::CANCELED->value && $this->ends_at) {
            return DateTimeHelper::parse($this->ends_at)->isFuture();
        }

        return false;
    }

    public function daysLeft(): int
    {
        if (!$this->isCurrentlyActive() && $this->status !== SubscriptionStatus::CANCELED->value) {
            return 0;
        }

        $targetDate = $this->ends_at ?? $this->current_period_end;
        if (!$targetDate) {
            return 0;
        }

        $date = DateTimeHelper::parse($targetDate);

        if ($date->isPast()) {
            return 0;
        }

        return (int) DateTimeHelper::now()->diffInDays($date);
    }

    public function displayStatus(): string
    {
        return match ($this->status) {
            SubscriptionStatus::ACTIVE->value => 'Active',
            SubscriptionStatus::TRIALING->value => 'Trialing',
            SubscriptionStatus::PAST_DUE->value => 'Past Due',
            SubscriptionStatus::CANCELED->value => $this->isCurrentlyActive() ? 'Canceled (Grace Period)' : 'Canceled',
            default => ucfirst($this->status),
        };
    }
}
