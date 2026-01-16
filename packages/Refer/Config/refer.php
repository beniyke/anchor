<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Refer Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Referral Code Settings
    |--------------------------------------------------------------------------
    */
    'code' => [
        'length' => 8,
        'prefix' => '',
        'uppercase' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rewards
    |--------------------------------------------------------------------------
    */
    'rewards' => [
        'referrer' => [
            'type' => 'fixed', // fixed, percentage
            'amount' => 100,   // Amount in minor units or percentage
            'currency' => 'USD',
        ],
        'referee' => [
            'type' => 'fixed',
            'amount' => 50,
            'currency' => 'USD',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reward Conditions
    |--------------------------------------------------------------------------
    */
    'conditions' => [
        'require_purchase' => false,
        'minimum_purchase' => 0,
        'reward_on' => 'signup', // signup, purchase, verification
    ],

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_referrals' => 0, // 0 = unlimited
        'code_expiry_days' => 0, // 0 = never expires
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => 'App\Models\User',
];
