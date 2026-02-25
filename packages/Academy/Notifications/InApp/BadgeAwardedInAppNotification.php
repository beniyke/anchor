<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class BadgeAwardedInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "You've been awarded the " . $this->payload->get('badge_name') . " badge!";
    }
}
