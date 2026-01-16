<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Repeat interval enumeration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Enums;

enum RepeatInterval: string
{
    case NONE = 'none';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Does not repeat',
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }

    /**
     * Check if this is a repeating interval.
     */
    public function repeats(): bool
    {
        return $this !== self::NONE;
    }
}
