<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class EnrolmentConfirmationEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Welcome to " . $this->payload->get('program_title');
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Enrolment Confirmed")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("You have successfully enrolled in **" . $this->payload->get('program_title') . "**.")
            ->action("Access Program", url($this->payload->get('url')))
            ->render();
    }
}
