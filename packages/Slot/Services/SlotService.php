<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service for managing slots and schedules.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Services;

use Database\BaseModel;
use Helpers\DateTimeHelper;
use InvalidArgumentException;
use RuntimeException;
use Slot\Enums\BookingStatus;
use Slot\Enums\ScheduleType;
use Slot\Interfaces\SlotServiceInterface;
use Slot\Models\SlotBooking;
use Slot\Models\SlotSchedule;
use Slot\Period;

class SlotService implements SlotServiceInterface
{
    public function createSchedule(BaseModel $schedulable, ScheduleType|string $type, Period $period, array $options = []): SlotSchedule
    {
        $options = array_merge($period->options, $options);
        $typeValue = $type instanceof ScheduleType ? $type->value : $type;

        if (! $this->validateOverlap($period, $type, $schedulable)) {
            throw new RuntimeException('Schedule conflicts with existing schedules');
        }

        $data = [
            'schedulable_type' => get_class($schedulable),
            'schedulable_id' => $schedulable->id,
            'type' => $typeValue,
            'starts_at' => $period->start,
            'ends_at' => $period->end,
            'title' => $options['title'] ?? null,
            'recurrence_rule' => isset($options['recurrence_rule']) && is_array($options['recurrence_rule'])
                ? json_encode($options['recurrence_rule'])
                : ($options['recurrence_rule'] ?? null),
            'recurrence_ends_at' => $options['recurrence_ends_at'] ?? null,
            'metadata' => $options['metadata'] ?? null,
            'overlap_rules' => $options['overlap_rules'] ?? null,
        ];

        return SlotSchedule::create($data);
    }

    public function updateSchedule(SlotSchedule $schedule, Period|array $data): bool
    {
        if ($data instanceof Period) {
            $period = $data;
            $data = array_merge([
                'starts_at' => $period->start,
                'ends_at' => $period->end,
            ], $period->options);
        }

        if (isset($data['starts_at']) || isset($data['ends_at'])) {
            $start = isset($data['starts_at']) ? DateTimeHelper::parse($data['starts_at']) : $schedule->starts_at;
            $end = isset($data['ends_at']) ? DateTimeHelper::parse($data['ends_at']) : $schedule->ends_at;
            $period = new Period($start, $end);

            $type = $data['type'] ?? $schedule->type;
            $schedulable = $schedule->schedulable;

            if (! $this->validateOverlap($period, $type, $schedulable, $schedule->id)) {
                throw new RuntimeException('Updated schedule conflicts with existing schedules');
            }
        }

        return $schedule->update($data);
    }

    public function deleteSchedule(SlotSchedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function generateSlots(BaseModel $schedulable, Period $range, ?int $duration = null, array $constraints = []): array
    {
        $duration = $duration ?? $range->options['slot_duration'] ?? null;

        if (! $duration) {
            throw new InvalidArgumentException('Slot duration must be provided explicitly or via Period options.');
        }

        $constraints = array_merge($range->options, $constraints);
        $bufferBefore = $constraints['buffer_before'] ?? 0;
        $bufferAfter = $constraints['buffer_after'] ?? 0;
        $gap = $constraints['gap'] ?? 0;

        $availabilities = SlotSchedule::forSchedulable($schedulable)
            ->type(ScheduleType::Availability)
            ->between($range->start, $range->end)
            ->get();

        $blockingSchedules = SlotSchedule::forSchedulable($schedulable)
            ->where(function ($query) {
                $query->type(ScheduleType::Appointment)
                    ->orWhere('type', ScheduleType::Blocked->value);
            })
            ->between($range->start, $range->end)
            ->get();

        $bookings = SlotBooking::query()
            ->whereIn('schedule_id', $availabilities->pluck('id'))
            ->active()
            ->between($range->start, $range->end)
            ->get();

        $availableSlots = [];

        foreach ($availabilities as $availability) {
            $occurrences = $availability->generateOccurrences($range->start, $range->end);

            foreach ($occurrences as $occurrence) {
                $period = new Period($occurrence['starts_at'], $occurrence['ends_at']);

                $slots = $period->split($duration, $gap);

                foreach ($slots as $slot) {
                    $bufferedSlot = $slot->addBuffer($bufferBefore, $bufferAfter);
                    $hasConflict = false;

                    foreach ($blockingSchedules as $blocking) {
                        $blockingPeriod = $blocking->getPeriod();
                        if ($bufferedSlot->overlaps($blockingPeriod)) {
                            $hasConflict = true;
                            break;
                        }
                    }

                    if (! $hasConflict) {
                        foreach ($bookings as $booking) {
                            $bookingPeriod = $booking->getPeriod();
                            if ($bufferedSlot->overlaps($bookingPeriod)) {
                                $hasConflict = true;
                                break;
                            }
                        }
                    }

                    if (! $hasConflict) {
                        $availableSlots[] = [
                            'starts_at' => $slot->start,
                            'ends_at' => $slot->end,
                            'duration' => $slot->duration(),
                            'availability_id' => $availability->id,
                        ];
                    }
                }
            }
        }

        return $availableSlots;
    }

    public function validateOverlap(Period $period, ScheduleType|string|null $type, BaseModel $schedulable, ?int $excludeScheduleId = null): bool
    {
        $type = $type ?? $period->options['type'] ?? null;

        if (! $type) {
            throw new InvalidArgumentException('Schedule type must be provided explicitly or via Period options.');
        }

        return empty($this->getConflicts($period, $type, $schedulable, $excludeScheduleId));
    }

    public function getConflicts(Period $period, ScheduleType|string|null $type, BaseModel $schedulable, ?int $excludeScheduleId = null): array
    {
        $type = $type ?? $period->options['type'] ?? null;

        if (! $type) {
            throw new InvalidArgumentException('Schedule type must be provided explicitly or via Period options.');
        }

        $typeEnum = $type instanceof ScheduleType ? $type : ScheduleType::from($type);
        $conflicts = [];
        $query = SlotSchedule::forSchedulable($schedulable)
            ->where(function ($q) use ($period) {
                $q->where('starts_at', '<', $period->end)
                    ->where('ends_at', '>', $period->start);
            });

        if ($excludeScheduleId) {
            $query->where('id', '!=', $excludeScheduleId);
        }

        $overlappingSchedules = $query->get();

        $conflicts = [];

        foreach ($overlappingSchedules as $schedule) {
            if (! $typeEnum->defaultOverlapRules()[$schedule->type->value]) {
                $conflicts[] = [
                    'schedule' => $schedule,
                    'reason' => "Type '{$typeEnum->value}' cannot overlap with '{$schedule->type->value}'",
                ];
            }
        }

        return $conflicts;
    }

    public function createBooking(
        SlotSchedule $schedule,
        BaseModel $bookable,
        Period $period,
        array $options = []
    ): SlotBooking {
        $options = array_merge($period->options, $options);

        $schedulePeriod = $schedule->getPeriod();
        if (! $schedulePeriod->encompasses($period)) {
            throw new RuntimeException('Booking period must be within schedule period');
        }

        $existingBookings = SlotBooking::query()
            ->where('schedule_id', $schedule->id)
            ->active()
            ->where(function ($q) use ($period) {
                $q->where('starts_at', '<', $period->end)
                    ->where('ends_at', '>', $period->start);
            })
            ->count();

        if ($existingBookings > 0) {
            throw new RuntimeException('Booking conflicts with existing bookings');
        }

        $data = [
            'schedule_id' => $schedule->id,
            'bookable_type' => get_class($bookable),
            'bookable_id' => $bookable->id,
            'starts_at' => $period->start,
            'ends_at' => $period->end,
            'status' => $options['status'] ?? BookingStatus::Pending->value,
            'metadata' => $options['metadata'] ?? null,
        ];

        return SlotBooking::create($data);
    }

    public function getUpcomingBookings(int $minutes): array
    {
        $now = DateTimeHelper::now();
        $limit = (clone $now)->addMinutes($minutes);

        return SlotBooking::query()
            ->confirmed()
            ->whereBetween('starts_at', [$now, $limit])
            ->get()
            ->all();
    }
}
