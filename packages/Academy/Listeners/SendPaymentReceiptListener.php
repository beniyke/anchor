<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\PaymentReceivedEvent;
use Academy\Notifications\InApp\PaymentReceiptInAppNotification;
use Academy\Notifications\Mail\PaymentReceiptEmailNotification;
use Helpers\Data\Data;
use Notify\Notify;

class SendPaymentReceiptListener
{
    public function handle(PaymentReceivedEvent $event): void
    {
        $enrolment = $event->enrolment;
        $user = $enrolment->user;

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_title' => $enrolment->program->title,
            'amount_formatted' => money($event->amount),
            'reference' => 'ACAD-' . $enrolment->id . '-' . time(),
            'date' => date('M d, Y'),
            'url' => config('academy.urls.payments', '/profile/payments'),
        ]);

        Notify::email(PaymentReceiptEmailNotification::class, $payload);
        Notify::inapp(PaymentReceiptInAppNotification::class, $payload);
    }
}
