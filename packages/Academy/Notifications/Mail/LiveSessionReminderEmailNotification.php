<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class LiveSessionReminderEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Reminder: Live Session starting soon";
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Live Session Reminder")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("The session **" . $this->payload->get('session_title') . "** is scheduled to start on **" . $this->payload->get('start_at') . "**.")
            ->action("Join Session", $this->payload->get('join_url'))
            ->render();
    }
}
