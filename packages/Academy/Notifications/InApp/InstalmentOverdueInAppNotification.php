<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class InstalmentOverdueInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "URGENT: Your payment of " . $this->payload->get('amount_formatted') . " is overdue.";
    }
}
