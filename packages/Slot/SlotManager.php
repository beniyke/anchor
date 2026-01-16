<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Managing slots, schedules, and bookings.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot;

use InvalidArgumentException;
use RuntimeException;
use Slot\Enums\ScheduleType;
use Slot\Interfaces\SlotServiceInterface;
use Slot\Models\SlotBooking;
use Slot\Models\SlotSchedule;
use Slot\Services\SlotAnalyticsService;

class SlotManager
{
    private SlotServiceInterface $service;

    private ?object $model = null;

    public function __construct(SlotServiceInterface $service)
    {
        $this->service = $service;
    }

    public function forModel(object $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function analytics(): SlotAnalyticsService
    {
        return resolve(SlotAnalyticsService::class);
    }

    public function schedule(Period $period, array $options = []): SlotSchedule
    {
        $this->ensureModelSet();

        $type = $period->options['type'] ?? null;

        if (! $type) {
            throw new InvalidArgumentException('Schedule type must be set on the Period object using asAppointment(), asAvailability(), etc.');
        }

        return $this->service->createSchedule($this->model, $type, $period, $options);
    }

    public function availability(Period $period, array $options = []): SlotSchedule
    {
        $this->ensureModelSet();

        return $this->service->createSchedule($this->model, ScheduleType::Availability, $period, $options);
    }

    public function appointment(Period $period, array $options = []): SlotSchedule
    {
        $this->ensureModelSet();

        return $this->service->createSchedule($this->model, ScheduleType::Appointment, $period, $options);
    }

    public function blocked(Period $period, array $options = []): SlotSchedule
    {
        $this->ensureModelSet();

        return $this->service->createSchedule($this->model, ScheduleType::Blocked, $period, $options);
    }

    public function custom(Period $period, array $options = []): SlotSchedule
    {
        $this->ensureModelSet();

        return $this->service->createSchedule($this->model, ScheduleType::Custom, $period, $options);
    }

    public function getAvailableSlots(Period $range, ?int $duration = null, array $constraints = []): array
    {
        $this->ensureModelSet();

        return $this->service->generateSlots($this->model, $range, $duration, $constraints);
    }

    public function checkConflicts(Period $period, ScheduleType|string|null $type = null): array
    {
        $this->ensureModelSet();

        return $this->service->getConflicts($period, $type, $this->model);
    }

    public function book(SlotSchedule $schedule, object $bookable, Period $period, array $options = []): SlotBooking
    {
        return $this->service->createBooking($schedule, $bookable, $period, $options);
    }

    public function updateSchedule(SlotSchedule $schedule, Period|array $data): bool
    {
        return $this->service->updateSchedule($schedule, $data);
    }

    public function deleteSchedule(SlotSchedule $schedule): bool
    {
        return $this->service->deleteSchedule($schedule);
    }

    public function getUpcomingBookings(int $minutes): array
    {
        return $this->service->getUpcomingBookings($minutes);
    }

    private function ensureModelSet(): void
    {
        if (! $this->model) {
            throw new RuntimeException('No model set. Call forModel() first.');
        }
    }
}
