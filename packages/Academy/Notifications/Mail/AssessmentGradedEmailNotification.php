<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class AssessmentGradedEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Assessment Graded: " . $this->payload->get('assessment_title');
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Assessment Graded")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Your submission for **" . $this->payload->get('assessment_title') . "** has been graded.")
            ->divider()
            ->attributes([
                'Score' => $this->payload->get('score') . '%',
                'Result' => $this->payload->get('is_passing') ? 'Passed' : 'Failed',
            ])
            ->action("View Results", url($this->payload->get('url')))
            ->render();
    }
}
