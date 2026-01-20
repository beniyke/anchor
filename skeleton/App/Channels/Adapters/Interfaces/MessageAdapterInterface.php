<?php

declare(strict_types=1);

namespace App\Channels\Adapters\Interfaces;

use Notify\Contracts\MessageNotifiable;

interface MessageAdapterInterface
{
    public function handle(MessageNotifiable $notification): mixed;
}
