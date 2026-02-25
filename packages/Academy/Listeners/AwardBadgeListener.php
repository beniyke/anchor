<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Listeners;

use Academy\Events\ProgramCompletedEvent;
use Academy\Services\BadgeService;

class AwardBadgeListener
{
    public function __construct(
        protected BadgeService $badgeService
    ) {
    }

    public function handle(ProgramCompletedEvent $event): void
    {
        $enrolment = $event->enrolment;
        $this->badgeService->checkTriggers($enrolment, 'completion');
    }
}
