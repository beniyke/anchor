<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Listeners;

use Academy\Events\PaymentSuccessfulEvent;
use Academy\Services\EnrolmentManagerService;
use Academy\Services\PaymentManagerService;

class ProcessPaymentSuccessListener
{
    public function __construct(
        protected PaymentManagerService $paymentManager,
        protected EnrolmentManagerService $enrolmentManager
    ) {
    }

    public function handle(PaymentSuccessfulEvent $event): void
    {
        // Record the payment
        $this->paymentManager->processPayment($event->enrolment, $event->reference, $event->amount);

        // Activate enrolment if it was pending
        $this->enrolmentManager->activate($event->enrolment);
    }
}
