<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * TicketRepliedNotification alerts users when an agent replies to their ticket.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class TicketRepliedNotification extends EmailNotification
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
        return "Reply to your ticket: " . $this->payload->get('subject');
    }

    public function getTitle(): string
    {
        return "New Ticket Reply";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $refid = $this->payload->get('refid');
        $message = $this->payload->get('reply_message');
        $url = $this->payload->get('ticket_url');

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->line("An agent has replied to your ticket **#{$refid}**.")
            ->line("---")
            ->line($message)
            ->line("---")
            ->action('View Ticket', url($url))
            ->render();
    }
}
