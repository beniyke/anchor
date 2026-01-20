<?php

declare(strict_types=1);

namespace App\Account\Notifications\Email;

use App\Core\BaseEmailNotification;
use Mail\Core\EmailComponent;

class EmailChangeEmailNotification extends BaseEmailNotification
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
        return 'Action Required: Verify Your New Email to Restore Account Access';
    }

    public function getTitle(): string
    {
        return 'Reactivate Your Account';
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $token = $this->payload->get('activation_token');

        return EmailComponent::make(false)
            ->greeting('Hello '.$name.',')
            ->line('We noticed that you\'ve recently updated the email address associated with your account.')
            ->line('To complete the process and reactivate your account, we need you to confirm the new email address.')
            ->action('Confirm Email', url('auth/activation/'.$token))
            ->line('Thank you for your cooperation.')
            ->line('Best regards.')
            ->render();
    }
}
