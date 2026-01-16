<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent for email campaigns.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class CampaignEmailNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name') ?? '',
            ],
        ];
    }

    public function getSubject(): string
    {
        return $this->payload->get('subject');
    }

    /**
     * Define the inner title (header) of the email.
     */
    public function getTitle(): string
    {
        return $this->getSubject();
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name') ?: 'there';
        $content = $this->payload->get('content');
        $unsubscribeUrl = $this->payload->get('unsubscribe_url');

        $builder = EmailComponent::make(false)
            ->greeting("Hello {$name},")
            ->line($this->parseContent($content, $name));

        if ($unsubscribeUrl) {
            $builder->subcopy("You received this because you're subscribed to our newsletter. <a href='{$unsubscribeUrl}'>Unsubscribe here</a>.");
        }

        return $builder->render();
    }

    protected function parseContent(string $content, string $name): string
    {
        $replacements = [
            '{name}' => $name,
            '{email}' => $this->payload->get('email'),
        ];

        return strtr($content, $replacements);
    }
}
