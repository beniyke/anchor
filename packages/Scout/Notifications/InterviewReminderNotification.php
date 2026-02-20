<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class InterviewReminderNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('to_email') => $this->payload->get('to_name'),
            ],
        ];
    }

    public function getSubject(): string
    {
        return "Interview Reminder: " . $this->payload->get('candidate_name');
    }

    public function getTitle(): string
    {
        return "Interview Reminder";
    }

    protected function getRawMessageContent(): string
    {
        $interviewerName = $this->payload->get('interviewer_name');
        $candidateName = $this->payload->get('candidate_name');
        $jobTitle = $this->payload->get('job_title');
        $date = $this->payload->get('date');
        $time = $this->payload->get('time');
        $location = $this->payload->get('location');
        $interviewId = $this->payload->get('interview_id');

        $urlPattern = config('scout.urls.application', 'scout/applications/{id}');
        $manageUrl = str_replace('{id}', (string)$interviewId, $urlPattern);

        return EmailComponent::make()
            ->greeting("Hello {$interviewerName},")
            ->markdown("This is a reminder for your upcoming interview with **{$candidateName}**.")
            ->divider()
            ->attributes([
                'Candidate' => $candidateName,
                'Position' => $jobTitle,
                'Date' => "{$date} at {$time}",
                'Location' => $location ?? 'Remote',
            ])
            ->divider()
            ->action('View Application', url($manageUrl))
            ->render();
    }
}
