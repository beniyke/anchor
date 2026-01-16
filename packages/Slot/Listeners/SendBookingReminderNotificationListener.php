<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Send Booking Reminder Notification
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Listeners;

use Helpers\Data;
use Mail\Mail;
use Slot\Events\BookingReminderEvent;
use Slot\Notifications\BookingReminderNotification;

class SendBookingReminderNotificationListener
{
    public function handle(BookingReminderEvent $event): void
    {
        $booking = $event->booking;
        $bookable = $booking->bookable;

        if ($bookable && isset($bookable->email)) {
            Mail::send(new BookingReminderNotification(Data::make([
                'name' => $bookable->name ?? 'Customer',
                'email' => $bookable->email,
                'title' => $booking->schedule->title ?? 'Appointment',
                'date' => $booking->starts_at->format('Y-m-d'),
                'time' => $booking->starts_at->format('H:i'),
                'view_url' => config('slot.urls.view_booking', 'bookings') . '/' . $booking->refid,
            ])));
        }
    }
}
