<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * StatusChangeNotification alerts clients when their account status changes.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class StatusChangeNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name'),
            ],
        ];
    }

    public function getSubject(): string
    {
        return "Account Status Update";
    }

    public function getTitle(): string
    {
        return "Status Update";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $statusLabel = $this->payload->get('status_label');
        $alertType = $this->payload->get('alert_type');

        $message = "Your account status has been updated to **{$statusLabel}**.";

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->status($message, $alertType)
            ->line("If you have any questions regarding this change, please contact support.")
            ->action('View Account', url($this->payload->get('account_url')))
            ->render();
    }
}
