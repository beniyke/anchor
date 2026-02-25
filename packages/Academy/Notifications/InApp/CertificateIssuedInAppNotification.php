<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class CertificateIssuedInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Your certificate for " . $this->payload->get('program_title') . " has been issued.";
    }
}
