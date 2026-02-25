<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class InstalmentDueEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Upcoming Payment Reminder";
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Upcoming Payment Due")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("This is a reminder that an instalment of **" . $this->payload->get('amount_formatted') . "** is due on **" . $this->payload->get('due_date') . "**.")
            ->action("Make Payment", url($this->payload->get('url')))
            ->render();
    }
}
