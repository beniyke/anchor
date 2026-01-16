<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Reseller Tier.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Enums;

enum ResellerTier: string
{
    case STANDARD = 'standard';
    case GOLD = 'gold';
    case PLATINUM = 'platinum';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Calculate cost based on tier discount from config.
     */
    public function calculateCost(int $baseCost): int
    {
        $discounts = config('ally.tier_discounts', []);
        $discount = $discounts[$this->value] ?? 0.0;

        return (int) ($baseCost * (1 - $discount));
    }
}
