<?php

declare(strict_types=1);

namespace App\Notifications\Email;

use App\Core\BaseEmailNotification;
use Mail\Core\EmailComponent;

class AccountVerificationEmailNotification extends BaseEmailNotification
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
        return 'Account Confirmation';
    }

    public function getTitle(): string
    {
        return 'Activate your account';
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name');
        $password = $this->payload->get('password');
        $token = $this->payload->get('activation_token');

        $component = EmailComponent::make(false)
            ->greeting('Hello '.$name.',');

        if ($password) {
            $component->line('Use the password below to access your account.')
                ->html($password, function (string $password) {
                    return '<h1>'.$password.'</h1>';
                })
                ->line('Ensure you change it immediately you login.');
        }

        return $component->line('Your account has been successfully created. To gain access to your account, you are required to activate it. Click the button below to activate your account.')
            ->action('Activate Account', url('auth/activation/'.$token))
            ->render();
    }
}
