<?php

declare(strict_types=1);

namespace App\Notifications\Email;

use App\Core\BaseEmailNotification;
use Mail\Core\EmailComponent;

class ResetPasswordEmailNotification extends BaseEmailNotification
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
        return 'Password Reset Link';
    }

    public function getTitle(): string
    {
        return 'Reset password';
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $token = $this->payload->get('reset_token');
        $expiration = $this->payload->get('token_expiration');

        return EmailComponent::make(false)
            ->greeting('Hi '.$name.',')
            ->line('You\'re receiving this e-mail because you requested a password reset for your account.')
            ->line('This link is valid till '.$expiration)
            ->line('If you didn\'t request this change, we recommend resetting your password for security.')
            ->action('Reset Password', url('auth/resetpassword/'.$token))
            ->render();
    }
}
