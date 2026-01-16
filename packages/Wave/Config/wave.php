<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wave configuration file.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Wave Default Configuration
    |--------------------------------------------------------------------------
    */

    'currency' => 'USD',

    'payment_strategy' => 'direct',

    'trial_days' => 0,

    'grace_period_days' => 30,

    'tax' => [
        'enabled' => true,
        'rate' => 15, // 15% VAT
        'inclusive' => false,
    ],

    'invoice' => [
        'prefix' => 'INV-',
        'url' => '/billing/invoices',
        'subscription_url' => '/billing/subscription',
        'payment_method_url' => '/billing/payment-method',
        'footer' => 'Thank you for your business!',
    ],

    'affiliate' => [
        'commission_rate' => 10, // 10%
    ],
];
