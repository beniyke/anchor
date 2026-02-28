<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Academy Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable Academy features.
    |
    */
    'features' => [
        'discussions' => true,
        'assessments' => true,
        'live_sessions' => true,
        'certificates' => true,
        'badges' => true,
        'waitlist' => true,
        'notes' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency & Payments
    |--------------------------------------------------------------------------
    |
    | Default currency for all programs.
    |
    */
    'currency' => 'USD',

    'instalments' => [
        'grace_period' => 3, // days
        'failed_payment_limit' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    |
    | Define high-level access rules.
    |
    */
    'access' => [
        'allow_guest_preview' => true,
        'require_email_verification' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificates
    |--------------------------------------------------------------------------
    |
    | Configuration for automated certificate generation.
    |
    */
    'certificates' => [
        'default_template' => 'academy::certificates.default',
        'enable_qr_code' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | URL prefix for program landing pages.
    |
    */
    'route_prefix' => 'programs',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Dynamic URLs for profile and academy sections.
    |
    */
    'urls' => [
        'payments' => 'profile/payments',
        'achievements' => 'profile/achievements',
        'submissions' => 'academy/submissions',
        'live_sessions' => 'academy/live-sessions',
        'programs' => 'academy/programs',
        'certificates' => 'academy/certificates',
    ],
    /*
    |--------------------------------------------------------------------------
    | Integration Toggles
    |--------------------------------------------------------------------------
    |
    | Enable or disable optional integrations with other Anchor packages.
    |
    */

    'integrations' => [
        'pay' => env('ACADEMY_INTEGRATION_PAY', true),
        'wallet' => env('ACADEMY_INTEGRATION_WALLET', true),
        'rank' => env('ACADEMY_INTEGRATION_RANK', true),
        'blish' => env('ACADEMY_INTEGRATION_BLISH', true),
        'refer' => env('ACADEMY_INTEGRATION_REFER', true),
        'link' => env('ACADEMY_INTEGRATION_LINK', true),
        'verify' => env('ACADEMY_INTEGRATION_VERIFY', true),
        'hub' => env('ACADEMY_INTEGRATION_HUB', true),
        'wave' => env('ACADEMY_INTEGRATION_WAVE', true),
        'audit' => env('ACADEMY_INTEGRATION_AUDIT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reward System
    |--------------------------------------------------------------------------
    |
    | Configuration for automated learning rewards via the Wallet package.
    |
    */

    'rewards' => [
        'enabled' => env('ACADEMY_REWARDS_ENABLED', false),
        'currency' => env('ACADEMY_REWARD_CURRENCY', 'USD'),
        'amounts' => [
            'lesson_completed' => 0,
            'module_completed' => 0,
            'program_completed' => 10.00,
            'badge_awarded' => 1.00,
            'quiz_passed' => 0.50,
        ],
    ],
];
