<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\EmailNotification;

abstract class AcademyEmailNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name') ?? '',
            ],
        ];
    }

    public function getTitle(): string
    {
        return $this->getSubject();
    }
}
