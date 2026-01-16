<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a reminder is due.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class ReminderNotification extends EmailNotification
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
        return 'Reminder: ' . $this->payload->get('message', 'You have a reminder');
    }

    public function getTitle(): string
    {
        return 'Reminder';
    }

    protected function getRawMessageContent(): string
    {
        $builder = EmailComponent::make()
            ->greeting("Hello {$this->payload->get('name')},")
            ->line('This is a reminder:')
            ->line("**{$this->payload->get('message')}**");

        if ($this->payload->get('resource_title')) {
            $builder->attributes([
                'Related to' => $this->payload->get('resource_title'),
            ]);
        }

        if ($this->payload->get('action_url')) {
            $builder->action('View Details', $this->payload->get('action_url'));
        }

        return $builder->render();
    }
}
