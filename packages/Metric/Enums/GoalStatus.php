<?php

declare(strict_types=1);

namespace Metric\Enums;

enum GoalStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}
