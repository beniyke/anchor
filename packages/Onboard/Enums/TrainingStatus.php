<?php

declare(strict_types=1);

namespace Onboard\Enums;

enum TrainingStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
