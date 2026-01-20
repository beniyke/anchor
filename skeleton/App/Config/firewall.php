<?php

declare(strict_types=1);

/**
 * Firewall Configuration
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

return [
    /**
     * Send Incident Notification to
     */
    'notification' => [
        'mail' => [
            'status' => true,
            'to' => ['name' => env('APP_NAME'), 'email' => env('MAIL_SENDER')],
        ],
    ],
    'drivers' => [
        Security\Firewall\Drivers\AuthFirewall::class,
        Security\Firewall\Drivers\AccountFirewall::class,
        Security\Firewall\Drivers\ApiRequestFirewall::class,
        Security\Firewall\Drivers\BlacklistFirewall::class,
        Security\Firewall\Drivers\HostFirewall::class,
        Security\Firewall\Drivers\MaintenanceFirewall::class,
    ],
    /**
     * Available Firewalls
     */
    'api-request' => [
        'identifier' => ['header' => 'authorization'],
        'enable' => env('FIREWALL_ACTIVATE_API_GUARD', false),
        'response' => ['code' => 429, 'message' => 'Too many request'],
        'method' => ['post', 'get', 'ajax', 'put', 'patch', 'delete'],
        /*
        Request Scheme to allow
         */
        'scheme' => [
            'enable' => true,
            'allow' => ['http', 'https'],
            'response' => ['code' => 403, 'message' => 'Forbidden'],
        ],
        /*
        Content type to allow
         */
        'content-type' => [
            'enable' => true,
            'method' => ['post', 'get', 'ajax', 'put', 'patch', 'delete'],
            'allow' => ['application/json', 'multipart/form-data', 'application/x-www-form-urlencoded'],
            'response' => ['code' => 403, 'message' => 'Forbidden'],
        ],
        /*
        Request to process per minute
         */
        'throttle' => [
            'attempt' => 100,
            'duration' => 60 * 60, // 1 hour
            'delay' => 30 * 60, // on exceeded delay for 5 minutes
        ],
        /*
        Routes
         */
        'routes' => ['api/{*}'],
    ],
    'maintenance' => [
        'enable' => env('FIREWALL_ACTIVATE_MAINTENANCE_GUARD', false),
        'allow' => [
            'routes' => ['auth/{logout}'],
            'ips' => [
                'ignore' => true,
                'list' => [],
            ],
            'browsers' => [
                'ignore' => true,
                'list' => [],
            ],
            'platforms' => [
                'ignore' => true,
                'list' => [],
            ],
            'devices' => [
                'ignore' => true,
                'list' => [],
            ],
        ],
    ],
    'blacklist' => [
        'enable' => env('FIREWALL_ACTIVATE_BLACKLIST_GUARD', false),
        'routes' => ['api/{*}'],
        'block' => [
            'ips' => [
                'dynamic' => ['127.0.0'],
                'specific' => [],
            ],
            'browsers' => [],
            'devices' => [],
            'platforms' => ['Windows 10'],
        ],
    ],
    'auth' => [
        'enable' => env('FIREWALL_ACTIVATE_AUTH_GUARD', false),
        'routes' => ['auth/login'],
        'identity' => 'email',
        'response' => 'You\'ve been temporarily suspended from accessing your account. Try again in {duration}.',
        'throttle' => [
            'attempt' => 3,
            'duration' => 60 * 60, // 1 hour,
            'delay' => 30 * 60, // delay for 30 minites
        ],
    ],
    'account' => [
        'enable' => env('FIREWALL_ACTIVATE_ACCOUNT_GUARD', false),
        'response' => 'You\'ve been temporarily suspended from accessing your account due to abuse. Try again {duration}.',
        'throttle' => [
            'request' => 100,
            'duration' => 60 * 60, // 1 hour,
            'delay' => 5 * (60), // delay for 5 minute
        ],
    ],
    'host' => [
        'enable' => env('FIREWALL_ACTIVATE_HOST_GUARD', false),
        'allow' => env('FIREWALL_HOST'),
    ],
];
