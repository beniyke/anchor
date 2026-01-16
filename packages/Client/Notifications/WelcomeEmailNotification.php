<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * WelcomeEmailNotification is sent to new clients upon registration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class WelcomeEmailNotification extends EmailNotification
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
        return "Welcome to " . config('app.name');
    }

    public function getTitle(): string
    {
        return "Welcome Aboard!";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->line("We are thrilled to welcome you to our platform. Your account has been successfully created.")
            ->line("You can now access your client portal to manage your services.")
            ->action('Login to Portal', url($this->payload->get('portal_url')))
            ->render();
    }
}
