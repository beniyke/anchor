<?php

declare(strict_types=1);

namespace Academy\Enums;

enum SubmissionStatus: string
{
    case PENDING = 'pending';
    case GRADED = 'graded';
    case RETURNED = 'returned';
    case LATE = 'late';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::GRADED => 'Graded',
            self::RETURNED => 'Returned for Revision',
            self::LATE => 'Late Submission',
        };
    }
}
