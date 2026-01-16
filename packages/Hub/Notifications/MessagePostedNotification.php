<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a new message is posted in a thread.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class MessagePostedNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => $this->payload->get('recipients', []),
        ];
    }

    public function getSubject(): string
    {
        $sender = $this->payload->get('sender_name', 'Someone');
        $threadTitle = $this->payload->get('thread_title', 'a conversation');

        return "New message from {$sender} in {$threadTitle}";
    }

    public function getTitle(): string
    {
        return 'New Message';
    }

    protected function getRawMessageContent(): string
    {
        $threadUrl = $this->payload->get('thread_url', '#');

        return EmailComponent::make()
            ->greeting("Hello,")
            ->line("**{$this->payload->get('sender_name')}** posted a new message in **{$this->payload->get('thread_title')}**:")
            ->line("\"{$this->payload->get('message_preview')}\"")
            ->action('View Conversation', $threadUrl)
            ->render();
    }
}
