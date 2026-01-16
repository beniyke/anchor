<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * LowCreditNotification alerts resellers when their distribution wallet balance is low.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class LowCreditNotification extends EmailNotification
{
    /**
     * Define the recipients of the email.
     */
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name'),
            ],
        ];
    }

    /**
     * Define the subject of the email.
     */
    public function getSubject(): string
    {
        $companyName = $this->payload->get('company_name');

        return "Low Distribution Credits Alert - {$companyName}";
    }

    /**
     * Define the title (header) of the email.
     */
    public function getTitle(): string
    {
        return "Low Credit Balance Warning";
    }

    /**
     * Define the body content of the email using EmailComponent.
     */
    protected function getRawMessageContent(): string
    {
        $companyName = $this->payload->get('company_name');
        $currentBalance = $this->payload->get('balance');
        $threshold = $this->payload->get('threshold');
        $dashboardUrl = $this->payload->get('dashboard_url');

        return EmailComponent::make()
            ->greeting("Hello {$companyName},")
            ->line("Your distribution credit balance has fallen to **{$currentBalance}**.")
            ->line("The notification threshold is set to {$threshold}.")
            ->status('Please top up your wallet to ensure uninterrupted service provision.', 'warning')
            ->action('Login to Dashboard', url($dashboardUrl))
            ->render();
    }
}
