<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\AssessmentGradedEvent;
use Academy\Notifications\InApp\AssessmentGradedInAppNotification;
use Academy\Notifications\Mail\AssessmentGradedEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendAssessmentGradedListener
{
    public function handle(AssessmentGradedEvent $event): void
    {
        $submission = $event->submission;
        $user = $submission->enrolment->user;
        $assessment = $submission->assessment;

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'assessment_title' => $assessment->title,
            'score' => $submission->score,
            'is_passing' => $submission->score >= ($assessment->passing_score ?? 0),
            'url' => config('academy.urls.submissions', 'academy/submissions') . "/{$submission->id}",
        ]);

        Notify::email(AssessmentGradedEmailNotification::class, $payload);
        Notify::inapp(AssessmentGradedInAppNotification::class, $payload);
    }
}
