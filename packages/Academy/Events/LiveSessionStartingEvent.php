<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyLiveSession;

class LiveSessionStartingEvent
{
    public function __construct(public AcademyLiveSession $session)
    {
    }
}
