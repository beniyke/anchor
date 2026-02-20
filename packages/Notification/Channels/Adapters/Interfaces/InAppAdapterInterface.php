<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for in-app notification adapters.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Channels\Adapters\Interfaces;

use Notify\Contracts\DatabaseNotifiable;

interface InAppAdapterInterface
{
    public function handle(DatabaseNotifiable $notification): mixed;
}
