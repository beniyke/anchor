<?php

declare(strict_types=1);

namespace App\Channels\Adapters;

use App\Channels\Adapters\Interfaces\SmsAdapterInterface;
use Notify\Contracts\MessageNotifiable;

class SmsAdapter implements SmsAdapterInterface
{
    public function handle(MessageNotifiable $notification): mixed
    {
        $payload = $notification->toMessage();

        $phone = $payload['recipient'];
        $message = $payload['message'];

        /**
         * Implement your logic to send message here
         */

        return true;
    }
}
