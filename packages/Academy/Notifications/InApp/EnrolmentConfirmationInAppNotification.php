<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class EnrolmentConfirmationInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Your enrolment in " . $this->payload->get('program_title') . " has been confirmed.";
    }
}
