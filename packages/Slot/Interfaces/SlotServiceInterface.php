<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for the Slot Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Interfaces;

use Database\BaseModel;
use Slot\Enums\ScheduleType;
use Slot\Models\SlotBooking;
use Slot\Models\SlotSchedule;
use Slot\Period;

interface SlotServiceInterface
{
    public function createSchedule(BaseModel $schedulable, ScheduleType|string $type, Period $period, array $options = []): SlotSchedule;

    public function updateSchedule(SlotSchedule $schedule, Period|array $data): bool;

    public function deleteSchedule(SlotSchedule $schedule): bool;

    public function generateSlots(BaseModel $schedulable, Period $range, ?int $duration = null, array $constraints = []): array;

    public function validateOverlap(Period $period, ScheduleType|string|null $type, BaseModel $schedulable, ?int $excludeScheduleId = null): bool;

    public function createBooking(SlotSchedule $schedule, BaseModel $bookable, Period $period, array $options = []): SlotBooking;

    public function getConflicts(Period $period, ScheduleType|string|null $type, BaseModel $schedulable, ?int $excludeScheduleId = null): array;

    public function getUpcomingBookings(int $minutes): array;
}
