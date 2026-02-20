<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * UrgentTicketAlert alerts agents when a high-priority or urgent ticket is created.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class UrgentTicketAlertNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('recipient_email') => $this->payload->get('recipient_name'),
            ],
        ];
    }

    public function getSubject(): string
    {
        return "URGENT: Escalated Ticket - " . $this->payload->get('subject');
    }

    public function getTitle(): string
    {
        return "Urgent Ticket Alert";
    }

    protected function getRawMessageContent(): string
    {
        $customerName = $this->payload->get('customer_name');
        $refid = $this->payload->get('refid');
        $subject = $this->payload->get('subject');
        $description = $this->payload->get('description');
        $url = $this->payload->get('manage_url');

        return EmailComponent::make()
            ->greeting("Hello,")
            ->status("An urgent ticket requires immediate attention.", 'error')
            ->markdown("Ticket **#{$refid}** from **{$customerName}** has been marked as Urgent.")
            ->attributes([
                'Subject' => $subject,
                'Reference' => "#{$refid}",
            ])
            ->divider()
            ->line($description)
            ->divider()
            ->action('Manage Ticket', url($url))
            ->render();
    }
}
