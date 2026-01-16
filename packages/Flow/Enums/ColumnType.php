<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Column Type
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Enums;

enum ColumnType: string
{
    case BACKLOG = 'backlog';
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case ARCHIVED = 'archived';
}
