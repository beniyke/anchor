<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * ally.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    'default_tier' => 'standard',
    'credit_conversion_rate' => 100,
    'auto_provision_wallet' => true,
    'low_credit_threshold' => 500,
    'tier_discounts' => [
        'platinum' => 0.30, // 30% discount
        'gold' => 0.15,     // 15% discount
        'standard' => 0.00,
    ],
    'urls' => [
        'dashboard' => 'partner/dashboard',
    ],
];
