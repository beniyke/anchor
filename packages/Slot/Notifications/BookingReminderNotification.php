<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Booking Reminder Notification
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class BookingReminderNotification extends EmailNotification
{
    /**
     * Define the email subject.
     */
    public function getSubject(): string
    {
        return "Reminder: Upcoming Appointment";
    }

    /**
     * Define who receives the email.
     */
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name'),
            ],
        ];
    }

    /**
     * Define the inner title (header) of the email.
     */
    public function getTitle(): string
    {
        return $this->getSubject();
    }

    /**
     * Define the email content.
     */
    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $title = $this->payload->get('title');
        $date = $this->payload->get('date');
        $time = $this->payload->get('time');
        $viewUrl = $this->payload->get('view_url');

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->line("This is a reminder for your upcoming **{$title}**.")
            ->line("Date: **{$date}**")
            ->line("Time: **{$time}**")
            ->line("Please let us know if you need to reschedule.")
            ->action('View Booking', url($viewUrl))
            ->render();
    }
}
