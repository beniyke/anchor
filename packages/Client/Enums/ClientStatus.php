<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ClientStatus Enum defines the possible states for a client entity.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Enums;

enum ClientStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Pending = 'pending';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Pending => 'Pending Verification',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Suspended => 'secondary',
            self::Pending => 'warning',
        };
    }

    public function alertType(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'error',
            default => 'warning',
        };
    }

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
