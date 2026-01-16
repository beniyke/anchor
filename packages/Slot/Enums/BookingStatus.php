<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Enum representing the status of a booking.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function isActive(): bool
    {
        return $this !== self::Cancelled;
    }

    public function canBeCancelled(): bool
    {
        return $this !== self::Cancelled;
    }

    public function canBeConfirmed(): bool
    {
        return $this === self::Pending;
    }
}
