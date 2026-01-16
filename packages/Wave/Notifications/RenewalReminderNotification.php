<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent as a renewal reminder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Notifications;

use Mail\Core\EmailComponent;

class RenewalReminderNotification extends WaveNotification
{
    public function getSubject(): string
    {
        return "Upcoming Subscription Renewal";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name') ?: 'Customer';
        $planName = $this->payload->get('plan_name');
        $renewalDate = $this->payload->get('renewal_date');
        $manageUrl = $this->payload->get('manage_url');

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->line("This is a reminder that your subscription to **{$planName}** is scheduled to renew on **{$renewalDate}**.")
            ->line("No action is required if you wish to continue your service.")
            ->action('Manage Subscription', url($manageUrl))
            ->render();
    }
}
