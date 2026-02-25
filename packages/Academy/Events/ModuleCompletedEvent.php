<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyModule;

class ModuleCompletedEvent
{
    public function __construct(public AcademyEnrolment $enrolment, public AcademyModule $module)
    {
    }
}
