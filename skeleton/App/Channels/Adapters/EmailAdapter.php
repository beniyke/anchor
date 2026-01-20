<?php

declare(strict_types=1);

namespace App\Channels\Adapters;

use App\Channels\Adapters\Interfaces\EmailAdapterInterface;
use Mail\Mailer;
use Notify\Contracts\EmailNotifiable;

class EmailAdapter implements EmailAdapterInterface
{
    protected Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function dispatch(EmailNotifiable $notification): mixed
    {
        return $this->mailer->send($notification);
    }
}
