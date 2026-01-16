<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Model representing a schedule for slots.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\HasMany;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;
use Slot\Enums\ScheduleType;
use Slot\Interfaces\SlotServiceInterface;
use Slot\Period;

/**
 * @property int             $id
 * @property string          $schedulable_type
 * @property int             $schedulable_id
 * @property ScheduleType    $type
 * @property string          $title
 * @property DateTimeHelper  $starts_at
 * @property DateTimeHelper  $ends_at
 * @property ?array          $recurrence_rule
 * @property ?DateTimeHelper $recurrence_ends_at
 * @property ?array          $metadata
 * @property ?array          $overlap_rules
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read BaseModel $schedulable
 * @property-read ModelCollection $bookings
 */
class SlotSchedule extends BaseModel
{
    protected string $table = 'slot_schedule';

    protected array $fillable = [
        'schedulable_type',
        'schedulable_id',
        'type',
        'title',
        'starts_at',
        'ends_at',
        'recurrence_rule',
        'recurrence_ends_at',
        'metadata',
        'overlap_rules',
    ];

    protected array $casts = [
        'type' => ScheduleType::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'recurrence_rule' => 'json',
        'recurrence_ends_at' => 'datetime',
        'metadata' => 'json',
        'overlap_rules' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function schedulable(): MorphTo
    {
        return $this->morphTo('schedulable');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(SlotBooking::class, 'schedule_id');
    }

    public function scopeType(Builder $query, ScheduleType|string $type): Builder
    {
        $typeValue = $type instanceof ScheduleType ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    public function scopeAvailability(Builder $query): Builder
    {
        return $this->scopeType($query, ScheduleType::Availability);
    }

    public function scopeAppointment(Builder $query): Builder
    {
        return $this->scopeType($query, ScheduleType::Appointment);
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $this->scopeType($query, ScheduleType::Blocked);
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $this->scopeType($query, ScheduleType::Custom);
    }

    public function scopeForSchedulable(Builder $query, BaseModel $model): Builder
    {
        return $query->where('schedulable_type', get_class($model))
            ->where('schedulable_id', $model->id);
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

    public function scopeOverlapping(Builder $query, DateTimeHelper|string $start, DateTimeHelper|string $end): Builder
    {
        $start = is_string($start) ? DateTimeHelper::parse($start) : $start;
        $end = is_string($end) ? DateTimeHelper::parse($end) : $end;

        return $query->where(function ($q) use ($start, $end) {
            $q->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start);
        });
    }

    public function canOverlapWith(ScheduleType|string $otherType): bool
    {
        $otherTypeValue = $otherType instanceof ScheduleType ? $otherType->value : $otherType;

        if (! empty($this->overlap_rules) && isset($this->overlap_rules[$otherTypeValue])) {
            return (bool) $this->overlap_rules[$otherTypeValue];
        }

        return $this->type->defaultOverlapRules()[$otherTypeValue] ?? false;
    }

    public function getPeriod(): Period
    {
        return new Period($this->starts_at, $this->ends_at);
    }

    public function generateOccurrences(DateTimeHelper $rangeStart, DateTimeHelper $rangeEnd): array
    {
        $occurrences = [];

        if (empty($this->recurrence_rule)) {
            if ($this->starts_at <= $rangeEnd && $this->ends_at >= $rangeStart) {
                $occurrences[] = [
                    'starts_at' => $this->starts_at,
                    'ends_at' => $this->ends_at,
                ];
            }

            return $occurrences;
        }

        $rule = $this->recurrence_rule;
        $frequency = $rule['frequency'] ?? 'daily';
        $interval = $rule['interval'] ?? 1;
        $recurrenceEnd = $this->recurrence_ends_at ?? $rangeEnd;

        $current = clone $this->starts_at;
        $duration = $this->ends_at->diffInMinutes($this->starts_at);

        while ($current <= $recurrenceEnd && $current <= $rangeEnd) {
            $occurrenceEnd = (clone $current)->addMinutes($duration);

            if ($current >= $rangeStart || $occurrenceEnd >= $rangeStart) {
                $occurrences[] = [
                    'starts_at' => clone $current,
                    'ends_at' => clone $occurrenceEnd,
                ];
            }

            switch ($frequency) {
                case 'daily':
                    $current = $current->addDays($interval);
                    break;
                case 'weekly':
                    $current = $current->addWeeks($interval);
                    break;
                case 'monthly':
                    $current = $current->addMonths($interval);
                    break;
                case 'yearly':
                    $current = $current->addYears($interval);
                    break;
                default:
                    break 2; // Unknown frequency, stop
            }
        }

        return $occurrences;
    }

    public function book(BaseModel $bookable, Period $period, array $options = []): SlotBooking
    {
        return resolve(SlotServiceInterface::class)->createBooking($this, $bookable, $period, $options);
    }

    public function updateSchedule(Period|array $data): bool
    {
        return resolve(SlotServiceInterface::class)->updateSchedule($this, $data);
    }
}
