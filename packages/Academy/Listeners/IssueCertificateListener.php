<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Listeners;

use Academy\Events\ProgramCompletedEvent;
use Academy\Services\CertificateService;

class IssueCertificateListener
{
    public function __construct(protected CertificateService $certificateService)
    {
    }

    public function handle(ProgramCompletedEvent $event): void
    {
        $this->certificateService->issue($event->enrolment);
    }
}
