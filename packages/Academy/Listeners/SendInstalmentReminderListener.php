<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\InstalmentOverdueEvent;
use Academy\Notifications\InApp\InstalmentOverdueInAppNotification;
use Academy\Notifications\Mail\InstalmentOverdueEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendInstalmentReminderListener
{
    public function handle(InstalmentOverdueEvent $event): void
    {
        $instalment = $event->instalment;
        $user = $instalment->enrolment->user;

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'amount_formatted' => money($instalment->amount),
            'due_date' => $instalment->due_at->format('M d, Y'),
            'url' => config('academy.urls.payments', 'profile/payments'),
        ]);

        Notify::email(InstalmentOverdueEmailNotification::class, $payload);
        Notify::inapp(InstalmentOverdueInAppNotification::class, $payload);
    }
}
