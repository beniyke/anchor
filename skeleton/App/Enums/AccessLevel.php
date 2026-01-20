<?php

declare(strict_types=1);

namespace App\Enums;

enum AccessLevel: string
{
    case Full = 'full';
    case Read = 'read';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isFullAccess(): bool
    {
        return $this === self::Full;
    }

    public function isReadOnly(): bool
    {
        return $this === self::Read;
    }

    public function label(): string
    {
        return match ($this) {
            self::Full => 'Full Access',
            self::Read => 'Read Only',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Full => 'success',
            self::Read => 'primary'
        };
    }

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
