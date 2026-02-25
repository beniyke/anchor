<?php

declare(strict_types=1);

namespace Academy\Events;

use Academy\Models\AcademyInstalment;

class InstalmentOverdueEvent
{
    public function __construct(public AcademyInstalment $instalment)
    {
    }
}
