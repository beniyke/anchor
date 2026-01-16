<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | ISO 4217 currency code used when creating wallets
    |
    */
    'default_currency' => env('WALLET_DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Auto-create Wallets
    |--------------------------------------------------------------------------
    |
    | Automatically create wallet on first transaction if it doesn't exist
    |
    */
    'auto_create' => env('WALLET_AUTO_CREATE', true),

    /*
    |--------------------------------------------------------------------------
    | Transaction Limits
    |--------------------------------------------------------------------------
    |
    | Maximum transaction amounts (in smallest currency unit - cents)
    | Set to null for no limit
    |
    */
    'limits' => [
        'credit' => [
            'min' => env('WALLET_CREDIT_MIN', 100), // $1.00
            'max' => env('WALLET_CREDIT_MAX', null),
        ],
        'debit' => [
            'min' => env('WALLET_DEBIT_MIN', 100),
            'max' => env('WALLET_DEBIT_MAX', null),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fee Configuration
    |--------------------------------------------------------------------------
    |
    | Default fee rules (can be overridden in database)
    |
    */
    'fees' => [
        'credit' => [
            'enabled' => false,
            'type' => 'PERCENTAGE', // FIXED, PERCENTAGE, TIERED
            'amount' => 0,
            'percentage' => 0.029, // 2.9%
        ],
        'debit' => [
            'enabled' => false,
            'type' => 'FIXED',
            'amount' => 200, // $2.00
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Processors
    |--------------------------------------------------------------------------
    |
    | Available payment processor adapters
    |
    */
    'processors' => [
        'stripe' => [
            'enabled' => env('WALLET_STRIPE_ENABLED', false),
            'api_key' => env('STRIPE_SECRET_KEY'),
        ],
        'paypal' => [
            'enabled' => env('WALLET_PAYPAL_ENABLED', false),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Log all wallet operations for audit purposes
    |
    */
    'logging' => [
        'enabled' => env('WALLET_LOGGING', true),
        'channel' => 'wallet', // Log file: storage/logs/wallet.log
    ],

    /*
    |--------------------------------------------------------------------------
    | Reconciliation
    |--------------------------------------------------------------------------
    |
    | Automatic balance reconciliation settings
    |
    */
    'reconciliation' => [
        'auto_fix' => env('WALLET_AUTO_FIX_BALANCE', true),
        'notify_on_mismatch' => env('WALLET_NOTIFY_MISMATCH', true),
    ],
];
