<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Enum representing the type of schedule.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Enums;

enum ScheduleType: string
{
    case Availability = 'availability';
    case Appointment = 'appointment';
    case Blocked = 'blocked';
    case Custom = 'custom';

    public function defaultOverlapRules(): array
    {
        return match ($this) {
            self::Availability => [
                'availability' => true,
                'appointment' => false,
                'blocked' => false,
                'custom' => false,
            ],
            self::Appointment => [
                'availability' => false,
                'appointment' => false,
                'blocked' => false,
                'custom' => false,
            ],
            self::Blocked => [
                'availability' => true,  // Blocked can overlap with availability (blocks it out)
                'appointment' => false,
                'blocked' => false,
                'custom' => false,
            ],
            self::Custom => [
                'availability' => false,
                'appointment' => false,
                'blocked' => false,
                'custom' => false,
            ],
        };
    }

    public function allowsOverlaps(): bool
    {
        return $this === self::Availability;
    }
}
