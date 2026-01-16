<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event dispatched when a message is posted.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Events;

use Hub\Models\Message;

class MessagePostedEvent
{
    public function __construct(
        public readonly Message $message,
        public readonly int $threadId,
        public readonly int $userId
    ) {
    }
}
