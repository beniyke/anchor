<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Listeners;

use Academy\Events\ProgramCompletedEvent;
use Academy\Notifications\Mail\ProgramCompletedEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendProgramCompletedNotificationListener
{
    public function handle(ProgramCompletedEvent $event): void
    {
        $enrolment = $event->enrolment;
        $user = $enrolment->user;
        $program = $enrolment->program;

        if (!$user || !$program) {
            return;
        }

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_title' => $program->title,
            'url' => config('academy.urls.programs', 'academy/programs') . "/{$program->slug}",
        ]);

        Notify::email(ProgramCompletedEmailNotification::class, $payload);
    }
}
