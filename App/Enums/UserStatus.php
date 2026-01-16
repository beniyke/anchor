<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Pending Verification',
            self::Suspended => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Suspended => 'secondary',
        };
    }

    public function canLogin(): bool
    {
        return match ($this) {
            self::Active => true,
            default => false,
        };
    }

    public function isRestricted(): bool
    {
        return match ($this) {
            self::Suspended => true,
            default => false,
        };
    }

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
