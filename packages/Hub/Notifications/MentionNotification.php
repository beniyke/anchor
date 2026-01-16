<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a user is mentioned in a message.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class MentionNotification extends EmailNotification
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
        $mentioner = $this->payload->get('mentioner_name', 'Someone');

        return "{$mentioner} mentioned you in a conversation";
    }

    public function getTitle(): string
    {
        return 'You were mentioned';
    }

    protected function getRawMessageContent(): string
    {
        $threadUrl = $this->payload->get('thread_url', '#');

        return EmailComponent::make()
            ->greeting("Hello {$this->payload->get('name')},")
            ->line("**{$this->payload->get('mentioner_name')}** mentioned you in **{$this->payload->get('thread_title')}**:")
            ->line("\"{$this->payload->get('message_preview')}\"")
            ->action('View Conversation', $threadUrl)
            ->render();
    }
}
