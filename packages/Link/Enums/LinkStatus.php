<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link status enumeration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Enums;

enum LinkStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
    case EXHAUSTED = 'exhausted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::EXPIRED => 'Expired',
            self::REVOKED => 'Revoked',
            self::EXHAUSTED => 'Usage Exhausted',
        };
    }

    public function isUsable(): bool
    {
        return $this === self::ACTIVE;
    }
}
