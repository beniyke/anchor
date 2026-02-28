<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\LiveSessionStartingEvent;
use Academy\Models\AcademyEnrolment;
use Academy\Notifications\InApp\LiveSessionReminderInAppNotification;
use Academy\Notifications\Mail\LiveSessionReminderEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendLiveSessionReminderListener
{
    public function handle(LiveSessionStartingEvent $event): void
    {
        $session = $event->session;
        $enrolments = AcademyEnrolment::where('program_id', $session->program_id)
            ->where('status', 'ACTIVE')
            ->get();

        foreach ($enrolments as $enrolment) {
            $user = $enrolment->user;

            $payload = Data::make([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'session_title' => $session->title,
                'start_at' => $session->scheduled_at->format('M d, Y H:i'),
                'join_url' => $session->join_url,
                'url' => config('academy.urls.live_sessions', 'academy/live-sessions') . "/{$session->id}",
            ]);

            Notify::email(LiveSessionReminderEmailNotification::class, $payload);
            Notify::inapp(LiveSessionReminderInAppNotification::class, $payload);
        }
    }
}
