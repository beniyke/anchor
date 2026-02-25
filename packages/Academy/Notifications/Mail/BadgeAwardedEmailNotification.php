<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class BadgeAwardedEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "You've Earned a Badge!";
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->status("New Achievement Unlocked", 'success')
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Congratulations! You have been awarded the **" . $this->payload->get('badge_name') . "** badge.")
            ->action("View Badge", url($this->payload->get('url')))
            ->render();
    }
}
