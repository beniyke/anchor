<?php

declare(strict_types=1);

namespace Academy\Enums;

enum SessionStatus: string
{
    case SCHEDULED = 'scheduled';
    case LIVE = 'live';
    case ENDED = 'ended';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::LIVE => 'Live Now',
            self::ENDED => 'Ended',
            self::CANCELLED => 'Cancelled',
        };
    }
}
