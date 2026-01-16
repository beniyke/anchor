<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reminder status enumeration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Enums;

enum ReminderStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case SNOOZED = 'snoozed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::SNOOZED => 'Snoozed',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * Check if status indicates actionable reminder.
     */
    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::SNOOZED], true);
    }
}
