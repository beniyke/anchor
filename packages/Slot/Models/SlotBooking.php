<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Model representing a booking for a slot.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\MorphTo;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Slot\Enums\BookingStatus;
use Slot\Period;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $schedule_id
 * @property string          $bookable_type
 * @property int             $bookable_id
 * @property DateTimeHelper  $starts_at
 * @property DateTimeHelper  $ends_at
 * @property BookingStatus   $status
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read SlotSchedule $schedule
 * @property-read BaseModel $bookable
 */
class SlotBooking extends BaseModel
{
    use HasRefid;

    protected string $table = 'slot_booking';

    protected array $fillable = [
        'refid',
        'schedule_id',
        'bookable_type',
        'bookable_id',
        'starts_at',
        'ends_at',
        'status',
        'metadata',
    ];

    protected array $casts = [
        'refid' => 'string',
        'status' => BookingStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SlotSchedule::class, 'schedule_id');
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo('bookable');
    }

    public function scopeStatus(Builder $query, BookingStatus|string $status): Builder
    {
        $statusValue = $status instanceof BookingStatus ? $status->value : $status;

        return $query->where('status', $statusValue);
    }

    public function scopePending(Builder $query): Builder
    {
        return $this->scopeStatus($query, BookingStatus::Pending);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $this->scopeStatus($query, BookingStatus::Confirmed);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $this->scopeStatus($query, BookingStatus::Cancelled);
    }

    public function scopeForBookable(Builder $query, BaseModel $model): Builder
    {
        return $query->where('bookable_type', get_class($model))
            ->where('bookable_id', $model->id);
    }

    public function scopeForSchedule(Builder $query, SlotSchedule $schedule): Builder
    {
        return $query->where('schedule_id', $schedule->id);
    }

    public function scopeBetween(Builder $query, DateTimeHelper|string $start, DateTimeHelper|string $end): Builder
    {
        $start = is_string($start) ? DateTimeHelper::parse($start) : $start;
        $end = is_string($end) ? DateTimeHelper::parse($end) : $end;

        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('starts_at', [$start, $end])
                ->orWhereBetween('ends_at', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('starts_at', '<=', $start)
                        ->where('ends_at', '>=', $end);
                });
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', BookingStatus::Cancelled->value);
    }

    public function cancel(): bool
    {
        if (! $this->status->canBeCancelled()) {
            return false;
        }

        $this->status = BookingStatus::Cancelled;

        return $this->save();
    }

    public function confirm(): bool
    {
        if (! $this->status->canBeConfirmed()) {
            return false;
        }

        $this->status = BookingStatus::Confirmed;

        return $this->save();
    }

    public function getPeriod(): Period
    {
        return new Period($this->starts_at, $this->ends_at);
    }
}
