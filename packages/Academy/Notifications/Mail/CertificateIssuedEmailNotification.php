<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class CertificateIssuedEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Your Certificate is Ready!";
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Certificate Issued")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Congratulations! Your certificate for **" . $this->payload->get('program_title') . "** is now available.")
            ->action("Download Certificate", url($this->payload->get('url')))
            ->render();
    }
}
