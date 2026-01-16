<?php

declare(strict_types=1);

namespace App\Channels;

use App\Channels\Adapters\Interfaces\WhatsAppAdapterInterface;
use Notify\Contracts\Channel;
use Notify\Contracts\Notifiable;

class WhatsAppChannel implements Channel
{
    protected WhatsAppAdapterInterface $adapter;

    public function __construct(WhatsAppAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function send(Notifiable $notification): mixed
    {
        return $this->adapter->handle($notification);
    }
}
