<?php

declare(strict_types=1);

namespace Academy\Enums;

enum BadgeStatus: string
{
    case ACTIVE = 'active';
    case REVOKED = 'revoked';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::REVOKED => 'Revoked',
        };
    }
}
