<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Events;

use Academy\Models\AcademyEnrolment;

class PaymentSuccessfulEvent
{
    public function __construct(
        public AcademyEnrolment $enrolment,
        public string $reference,
        public int $amount
    ) {
    }
}
