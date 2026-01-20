<?php

declare(strict_types=1);

namespace App\Channels\Adapters\Interfaces;

use Notify\Contracts\EmailNotifiable;

interface EmailAdapterInterface
{
    public function dispatch(EmailNotifiable $notification): mixed;
}
