<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class PaymentReceiptInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Your payment for " . $this->payload->get('program_title') . " was successful.";
    }
}
