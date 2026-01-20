<?php

declare(strict_types=1);

namespace App\Channels;

use App\Channels\Adapters\Interfaces\EmailAdapterInterface;
use Notify\Contracts\Channel;
use Notify\Contracts\Notifiable;

class EmailChannel implements Channel
{
    protected EmailAdapterInterface $adapter;

    public function __construct(EmailAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function send(Notifiable $notification): mixed
    {
        return $this->adapter->dispatch($notification);
    }
}
