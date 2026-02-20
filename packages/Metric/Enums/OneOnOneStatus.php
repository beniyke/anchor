<?php

declare(strict_types=1);

namespace Metric\Enums;

enum OneOnOneStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
