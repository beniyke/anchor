<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event dispatched when a reminder is due.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Events;

use Hub\Models\Reminder;

class ReminderDueEvent
{
    public function __construct(
        public readonly Reminder $reminder,
        public readonly int $userId
    ) {
    }
}
