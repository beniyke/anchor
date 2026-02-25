<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class InstalmentDueInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Your instalment of " . $this->payload->get('amount_formatted') . " is due on " . $this->payload->get('due_date') . ".";
    }
}
