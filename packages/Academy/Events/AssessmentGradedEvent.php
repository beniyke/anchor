<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademySubmission;

class AssessmentGradedEvent
{
    public function __construct(public AcademySubmission $submission)
    {
    }
}
