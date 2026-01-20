<?php

declare(strict_types=1);

namespace App\Channels;

use App\Channels\Adapters\Interfaces\InAppAdapterInterface;
use Notify\Contracts\Channel;
use Notify\Contracts\Notifiable;

class InAppChannel implements Channel
{
    protected InAppAdapterInterface $adapter;

    public function __construct(InAppAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function send(Notifiable $notification): mixed
    {
        return $this->adapter->handle($notification);
    }
}
