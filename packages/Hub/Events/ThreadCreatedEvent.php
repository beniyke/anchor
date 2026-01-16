<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event dispatched when a thread is created.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Events;

use Hub\Models\Thread;

class ThreadCreatedEvent
{
    public function __construct(
        public readonly Thread $thread,
        public readonly int $createdBy
    ) {
    }
}
