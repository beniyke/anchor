<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification class for sending OTP via Email.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Channels\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class SendOtpEmailNotification extends EmailNotification
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
        return 'Your Verification Code';
    }

    public function getPreheader(): ?string
    {
        return "Your verification code is {$this->payload->get('code')}. It expires in 15 minutes.";
    }

    public function getTitle(): string
    {
        return 'Verification Code';
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $code = $this->payload->get('code');

        return EmailComponent::make(false)
            ->greeting('Hello ' . $name ?? 'User' . ',')
            ->line('You have requested a verification code. Please use the code below to complete your verification.')
            ->html($code, function (string $code) {
                return '<div style="background: #f4f4f4; padding: 20px; text-align: center; border-radius: 8px;">' .
                    '<h1 style="font-size: 32px; letter-spacing: 5px; margin: 0;">' . $code . '</h1>' .
                    '</div>';
            })
            ->line('This code will expire in 15 minutes.')
            ->line('If you did not request this code, please ignore this email.')
            ->line('This is an automated message, please do not reply.')
            ->render();
    }
}
