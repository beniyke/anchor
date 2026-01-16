<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event dispatched when a user is mentioned.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Events;

use Hub\Models\Mention;
use Hub\Models\Message;

class UserMentionedEvent
{
    public function __construct(
        public readonly Mention $mention,
        public readonly Message $message,
        public readonly int $mentionedUserId
    ) {
    }
}
