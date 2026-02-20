<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * In-app notification adapter.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Channels\Adapters;

use Notification\Channels\Adapters\Interfaces\InAppAdapterInterface;
use Notification\Services\NotificationManagerService;
use Notify\Contracts\DatabaseNotifiable;

class InAppAdapter implements InAppAdapterInterface
{
    protected NotificationManagerService $service;

    public function __construct(NotificationManagerService $service)
    {
        $this->service = $service;
    }

    public function handle(DatabaseNotifiable $notification): mixed
    {
        $payload = $notification->toDatabase();

        return $this->service->notifyUser($payload);
    }
}
