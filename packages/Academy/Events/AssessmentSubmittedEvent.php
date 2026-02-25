<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademySubmission;

class AssessmentSubmittedEvent
{
    public function __construct(public AcademySubmission $submission)
    {
    }
}
