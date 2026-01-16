<?php

declare(strict_types=1);

namespace App\Channels;

use App\Channels\Adapters\Interfaces\SmsAdapterInterface;
use Notify\Contracts\Channel;
use Notify\Contracts\Notifiable;

class SmsChannel implements Channel
{
    protected SmsAdapterInterface $adapter;

    public function __construct(SmsAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function send(Notifiable $notification): mixed
    {
        return $this->adapter->handle($notification);
    }
}
