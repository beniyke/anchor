<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a user replies to a thread.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class PostReplyNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'bcc' => $this->payload->get('recipients', []),
        ];
    }

    public function getSubject(): string
    {
        $sender = $this->payload->get('sender_name', 'Someone');
        $title = $this->payload->get('thread_title');

        return "{$sender} replied to \"{$title}\"";
    }

    public function getTitle(): string
    {
        return 'New Reply';
    }

    protected function getRawMessageContent(): string
    {
        $threadUrl = $this->payload->get('thread_url', '#');

        return EmailComponent::make()
            ->greeting("Hello,")
            ->line("**{$this->payload->get('sender_name')}** replied to the thread **{$this->payload->get('thread_title')}**:")
            ->line("\"{$this->payload->get('message_preview')}\"")
            ->action('View Reply', $threadUrl)
            ->render();
    }
}
