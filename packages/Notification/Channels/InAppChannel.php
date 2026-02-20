<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * In-app notification channel.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Channels;

use Notification\Channels\Adapters\Interfaces\InAppAdapterInterface;
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
