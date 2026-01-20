<?php

declare(strict_types=1);

namespace App\Channels\Adapters\Interfaces;

use Notify\Contracts\DatabaseNotifiable;

interface InAppAdapterInterface
{
    public function handle(DatabaseNotifiable $notification): mixed;
}
