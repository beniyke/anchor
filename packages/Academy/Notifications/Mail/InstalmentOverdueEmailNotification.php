<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class InstalmentOverdueEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Urgent: Payment Overdue";
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->status("Overdue", 'danger')
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Your instalment of **" . $this->payload->get('amount_formatted') . "** was due on **" . $this->payload->get('due_date') . "** and is now overdue.")
            ->action("Pay Now", url($this->payload->get('url')))
            ->render();
    }
}
