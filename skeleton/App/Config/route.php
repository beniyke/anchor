<?php

declare(strict_types=1);

/**
 * Routing configuration for the application.
 *
 * This configuration defines default routes, API routes, redirects,
 * and named routes for easier access within the application.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

return [
    /**
     * Default route when none is specified
     */
    'default' => 'website/home',

    /**
     * Placeholder for route substitutions (dynamic routing)
     */
    'substitute' => [],

    /**
     * Authentication middleware configuration
     */
    'auth' => [
        /**
         * Web middleware-protected routes
         */
        'web' => ['auth/{*}', 'account/{*}'],

        /**
         * API middleware-protected routes
         */
        'api' => ['api/{*}'],
    ],

    /**
     * List of API routes
     */
    'api' => ['api/{*}'],

    /**
     * Redirect routes for user flows
     */
    'redirect' => [
        'login' => 'auth/login',
        'signup' => 'auth/signup',
        'doc' => 'docs/learn'
    ],

    /**
     * Named routes for internal referencing
     */
    'names' => [
        'home' => 'account/home',
        'profile' => 'account/profile',
        'change-password' => 'account/changepassword',
        'change-photo' => 'account/changephoto',
        'login' => 'auth/login',
        'logout' => 'auth/logout',
        'forgot-password' => 'auth/recoverpassword',
        'activity' => 'account/activity',
        'notification' => 'account/notification',
        'setting' => 'account/setting',
    ],

    /**
     * Routes excluded from auth middleware
     */
    'auth-exclude' => [
        'web' => [
            'auth/{login, recoverpassword, resetpassword, signup, activation}',
        ],
        'api' => [
            'api/{auth}',
        ],
    ],

    /**
     * All login route paths
     */
    'login' => [
        'auth/login',
    ],

    /**
     * All logout route paths
     */
    'logout' => [
        'auth/logout',
    ],

    /**
     * Custom routes used in the application
     */
    'custom' => [
        'account/profile',
        'account/changephoto',
        'account/changepassword',
        'account/activity',
        'account/notification',
        'account/setting',
        'auth/logout',
    ],
];
