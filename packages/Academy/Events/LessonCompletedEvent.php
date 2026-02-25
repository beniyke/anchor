<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyProgress;

class LessonCompletedEvent
{
    public function __construct(public AcademyProgress $progress)
    {
    }
}
