<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleType: string
{
    case SuperAdmin = 'super-admin';
    case SeniorAdmin = 'senior-admin';
    case Admin = 'admin';
    case User = 'user';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function isSeniorAdmin(): bool
    {
        return $this === self::SeniorAdmin;
    }

    public function isAdmin(): bool
    {
        return in_array($this, [self::SuperAdmin, self::SeniorAdmin, self::Admin], true);
    }

    public function isUser(): bool
    {
        return $this === self::User;
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::SeniorAdmin => 'Senior Admin',
            self::Admin => 'Admin',
            self::User => 'User',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::SeniorAdmin => 'warning',
            self::Admin => 'info',
            self::User => 'secondary',
        };
    }

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
