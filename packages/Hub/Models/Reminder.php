<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reminder model for scheduled reminders.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;
use Hub\Enums\ReminderStatus;
use Hub\Enums\RepeatInterval;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $user_id
 * @property ?string         $remindable_type
 * @property ?int            $remindable_id
 * @property string          $message
 * @property DateTimeHelper  $remind_at
 * @property ?RepeatInterval $repeat_interval
 * @property ReminderStatus  $status
 * @property ?DateTimeHelper $completed_at
 * @property ?DateTimeHelper $snoozed_until
 * @property int             $snooze_count
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ?BaseModel $remindable
 */
class Reminder extends BaseModel
{
    protected string $table = 'hub_reminder';

    protected array $fillable = [
        'refid',
        'user_id',
        'remindable_type',
        'remindable_id',
        'message',
        'remind_at',
        'repeat_interval',
        'status',
        'completed_at',
        'snoozed_until',
        'snooze_count',
        'metadata',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'remind_at' => 'datetime',
        'repeat_interval' => RepeatInterval::class,
        'status' => ReminderStatus::class,
        'completed_at' => 'datetime',
        'snoozed_until' => 'datetime',
        'snooze_count' => 'int',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function remindable(): MorphTo
    {
        return $this->morphTo('remindable');
    }

    public function isDue(): bool
    {
        if (!$this->status?->isActive()) {
            return false;
        }

        $checkTime = $this->snoozed_until ?? $this->remind_at;

        return $checkTime !== null && $checkTime->isPast();
    }

    public function repeats(): bool
    {
        return $this->repeat_interval?->repeats() ?? false;
    }

    public function complete(): void
    {
        $this->update([
            'status' => ReminderStatus::COMPLETED,
            'completed_at' => DateTimeHelper::now(),
        ]);
    }

    public function snooze(int $minutes = 15): void
    {
        $this->update([
            'status' => ReminderStatus::SNOOZED,
            'snoozed_until' => DateTimeHelper::now()->addMinutes($minutes),
            'snooze_count' => ($this->snooze_count ?? 0) + 1,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => ReminderStatus::CANCELLED]);
    }

    /**
     * Reschedule for next occurrence (for repeating reminders).
     */
    public function rescheduleNext(): void
    {
        if (!$this->repeats()) {
            return;
        }

        $nextDate = match ($this->repeat_interval) {
            RepeatInterval::DAILY => $this->remind_at->addDay(),
            RepeatInterval::WEEKLY => $this->remind_at->addWeek(),
            RepeatInterval::MONTHLY => $this->remind_at->addMonth(),
            RepeatInterval::YEARLY => $this->remind_at->addYear(),
            default => null,
        };

        if ($nextDate !== null) {
            $this->update([
                'remind_at' => $nextDate,
                'status' => ReminderStatus::PENDING,
                'snoozed_until' => null,
                'snooze_count' => 0,
            ]);
        }
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDue(Builder $query): Builder
    {
        $now = DateTimeHelper::now();

        return $query->whereIn('status', [ReminderStatus::PENDING, ReminderStatus::SNOOZED])
            ->where(function ($q) use ($now) {
                $q->where(function ($q2) use ($now) {
                    $q2->whereNull('snoozed_until')
                        ->where('remind_at', '<=', $now);
                })->orWhere('snoozed_until', '<=', $now);
            });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReminderStatus::PENDING);
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        $future = DateTimeHelper::now()->addDays($days);

        return $this->scopePending($query)
            ->where('remind_at', '<=', $future)
            ->orderBy('remind_at', 'asc');
    }
}
