<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyBadgeAward;

class BadgeAwardedEvent
{
    public function __construct(public AcademyBadgeAward $award)
    {
    }
}
