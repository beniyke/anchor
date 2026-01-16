<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Booking Reminder Event
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Events;

use Slot\Models\SlotBooking;

class BookingReminderEvent
{
    public SlotBooking $booking;

    public function __construct(SlotBooking $booking)
    {
        $this->booking = $booking;
    }
}
