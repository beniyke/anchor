<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class AssessmentGradedInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Your " . $this->payload->get('assessment_title') . " assessment has been graded. Score: " . $this->payload->get('score') . "%.";
    }
}
