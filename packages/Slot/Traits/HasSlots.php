<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait to add slot scheduling capabilities to models.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Traits;

use Database\Relations\HasManyThrough;
use Database\Relations\MorphMany;
use InvalidArgumentException;
use Slot\Enums\ScheduleType;
use Slot\Interfaces\SlotServiceInterface;
use Slot\Models\SlotBooking;
use Slot\Models\SlotSchedule;
use Slot\Period;

trait HasSlots
{
    public function schedules(): MorphMany
    {
        return $this->morphMany(SlotSchedule::class, 'schedulable');
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(
            SlotBooking::class,
            SlotSchedule::class,
            'schedulable_id',
            'schedule_id',
            'id',
            'id'
        )->where('schedulable_type', static::class);
    }

    public function schedule(Period $period, array $options = []): SlotSchedule
    {
        $type = $period->options['type'] ?? null;

        if (! $type) {
            throw new InvalidArgumentException('Schedule type must be set on the Period object.');
        }

        return $this->getSlotService()->createSchedule($this, $type, $period, $options);
    }

    public function availability(Period $period, array $options = []): SlotSchedule
    {
        return $this->getSlotService()->createSchedule($this, ScheduleType::Availability, $period, $options);
    }

    public function appointment(Period $period, array $options = []): SlotSchedule
    {
        return $this->getSlotService()->createSchedule($this, ScheduleType::Appointment, $period, $options);
    }

    public function blocked(Period $period, array $options = []): SlotSchedule
    {
        return $this->getSlotService()->createSchedule($this, ScheduleType::Blocked, $period, $options);
    }

    public function custom(Period $period, array $options = []): SlotSchedule
    {
        return $this->getSlotService()->createSchedule($this, ScheduleType::Custom, $period, $options);
    }

    public function updateSchedule(SlotSchedule $schedule, Period|array $data): bool
    {
        return $this->getSlotService()->updateSchedule($schedule, $data);
    }

    public function deleteSchedule(SlotSchedule $schedule): bool
    {
        return $this->getSlotService()->deleteSchedule($schedule);
    }

    public function getAvailableSlots(Period $range, ?int $duration = null, array $constraints = []): array
    {
        return $this->getSlotService()->generateSlots($this, $range, $duration, $constraints);
    }

    public function hasConflict(Period $period, ScheduleType|string|null $type = null): bool
    {
        return ! empty($this->getConflicts($period, $type));
    }

    public function getConflicts(Period $period, ScheduleType|string|null $type = null): array
    {
        return $this->getSlotService()->getConflicts($period, $type, $this);
    }

    protected function getSlotService(): SlotServiceInterface
    {
        return resolve(SlotServiceInterface::class);
    }
}
