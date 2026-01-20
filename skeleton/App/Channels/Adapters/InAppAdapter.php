<?php

declare(strict_types=1);

namespace App\Channels\Adapters;

use App\Channels\Adapters\Interfaces\InAppAdapterInterface;
use App\Services\NotificationService;
use Notify\Contracts\DatabaseNotifiable;

class InAppAdapter implements InAppAdapterInterface
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function handle(DatabaseNotifiable $notification): mixed
    {
        $payload = $notification->toDatabase();

        return $this->service->notifyUser($payload);
    }
}
