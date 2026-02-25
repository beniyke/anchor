<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyEnrolment;

class LearnerSuspendedEvent
{
    public function __construct(public AcademyEnrolment $enrolment)
    {
    }
}
