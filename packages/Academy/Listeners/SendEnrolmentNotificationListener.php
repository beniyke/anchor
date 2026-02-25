<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Listeners;

use Academy\Events\EnrolmentCreatedEvent;
use Academy\Notifications\InApp\EnrolmentConfirmationNotification;
use Academy\Notifications\Mail\EnrolmentConfirmationMail;
use Academy\Services\PaymentManagerService;
use Helpers\Data\Data;
use Notify\Notify;

class SendEnrolmentNotificationListener
{
    public function __construct(protected PaymentManagerService $paymentManager)
    {
    }

    public function handle(EnrolmentCreatedEvent $event): void
    {
        $enrolment = $event->enrolment;
        $user = $enrolment->user;
        $program = $enrolment->program;

        // Initialize instalments if needed
        $this->paymentManager->initializeInstalments($enrolment);

        $payload = Data::make([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'program_title' => $program->title,
            'url' => config('academy.urls.programs', '/academy/programs') . "/{$program->slug}",
        ]);

        // Send Email
        Notify::email(EnrolmentConfirmationMail::class, $payload);

        Notify::inapp(EnrolmentConfirmationNotification::class, $payload);
    }
}
