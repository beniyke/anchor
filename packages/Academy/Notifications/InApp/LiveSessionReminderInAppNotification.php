<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class LiveSessionReminderInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "The live session '" . $this->payload->get('session_title') . "' is starting soon.";
    }
}
