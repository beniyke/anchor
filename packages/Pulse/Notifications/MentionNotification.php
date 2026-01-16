<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a user is mentioned in a post.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Notifications;

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

        return "{$mentioner} mentioned you in a post";
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
            ->action('View Post', $threadUrl)
            ->render();
    }
}
