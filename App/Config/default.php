<?php

declare(strict_types=1);

/**
 * Configuration settings for the application.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

return [
    /**
     * The encryption key for securing sensitive data.
     * Key used to encrypt and secure sensitive information.
     */
    'encryption_key' => env('APP_KEY'),
    'previous_keys' => env('APP_PREVIOUS_KEYS', ''),

    /**
     * The default timezone for the application.
     * Sets the default timezone, affecting date and time functions throughout the application.
     */
    'timezone' => env('TIMEZONE', 'UTC'),

    /**
     * The name of the application.
     */
    'name' => env('APP_NAME', 'Anchor'),

    /**
     * The host for the application, defaulting to 'localhost/anchor'.
     * This is used by the console to properly generate URLs when using the command line tool.
     */
    'host' => env('APP_HOST', 'localhost/anchor'),

    /**
     * Whether the application is running in secure mode (HTTPS).
     * Indicates if the application is using HTTPS for secure connections.
     */
    'secure' => env('APP_SECURE', false),

    /**
     * Debug mode; if true, more detailed error messages are shown.
     * Enables detailed error messages for debugging purposes.
     */
    'debug' => env('APP_DEBUG', true),

    /**
     * Application environment (e.g., 'prod', 'dev', 'test').
     * Used to determine the current environment and load appropriate configurations.
     */
    'env' => env('APP_ENV', 'dev'),

    /**
     * Authentication settings.
     * Includes configuration for password policies and security features.
     */
    'auth' => [
        'password_max_age_days' => env('PASSWORD_MAX_AGE_DAYS', 30),
    ],

    /**
     * CSRF (Cross-Site Request Forgery) settings.
     */
    'csrf' => [
        /**
         * Enable or disable CSRF protection.
         * Activates CSRF protection to mitigate cross-site request forgery attacks.
         */
        'enable' => true,

        /**
         * Honeypot implementation for additional security.
         * Uses a honeypot technique to deter automated bots from submitting forms.
         */
        'honeypot' => true,

        /**
         * Check the origin of requests to protect against CSRF.
         * Disables origin header checks; can be enabled for stricter validation.
         */
        'origin_check' => false,

        /**
         * Persist CSRF tokens across sessions.
         * Allows CSRF tokens to be valid across multiple user sessions for improved usability.
         */
        'persist' => true,

        /**
         * Routes that should be excluded from CSRF protection.
         * Excludes all API routes from CSRF checks, as they are typically stateless.
         */
        'routes' => [
            'exclude' => ['api/{*}'],
        ],
    ],

    /**
     * Session settings.
     */
    'session' => [
        /**
         * The name of the session cookie used to store session data.
         * Specifies the name of the session cookie for tracking user sessions.
         */
        'name' => 'anchor_session',

        /**
         * Duration before the session times out (in seconds).
         * This determines how long the session will remain active before being invalidated.
         */
        'timeout' => env('SESSION_TIMEOUT', 14400),

        /**
         * Whether to regenerate the session ID on certain actions for security.
         * Regenerating the session ID helps prevent session fixation attacks.
         */
        'regenerate' => env('SESSION_REGENERATE', true),

        /**
         * Session Garbage Collection Lottery.
         * Determines the probability of the GC running on any given request.
         * Format: [chances, out_of] (e.g., [2, 100] = 2% chance).
         */
        'lottery' => [2, 100],

        /**
         * Cookie Setting
         */
        'cookie' => [
            /**
             * The default lifetime for session cookies (in seconds).
             * 0 means the cookie expires when the browser is closed (standard session).
             */
            'lifetime' => 0,

            /**
             * Domain the cookie is available to (null means the current host).
             * Should be null unless using subdomains (e.g., '.example.com').
             */
            'domain' => env('SESSION_COOKIE_DOMAIN', null),

            /**
             * Path on the server the cookie is available to. Usually the root ('/').
             */
            'path' => '/',

            /**
             * If true, cookies will only be sent over HTTPS.
             * This MUST be true in production. Defaults to APP_SECURE setting.
             */
            'secure' => env('SESSION_COOKIE_SECURE', env('APP_SECURE', false)),

            /**
             * If true, cookies will only be accessible via the HTTP protocol (not JavaScript).
             * This MUST be true for session and auth cookies.
             */
            'http_only' => env('SESSION_COOKIE_HTTPONLY', true),

            /**
             * SameSite policy for CSRF protection ('Lax', 'Strict', or 'None').
             * 'Lax' is the recommended default for session cookies.
             */
            'samesite' => env('SESSION_COOKIE_SAMESITE', 'Lax'),

            /**
             * The long lifetime for 'Remember Me' functionality (30 days).
             * Used to set the cookie lifetime when the user opts for persistent login.
             */
            'remember_me_lifetime' => 2592000, // 30 days in seconds
        ],
    ],

    /**
     * Security Headers Configuration
     */
    'security_headers' => [
        /**
         * Enable or disable security headers middleware.
         */
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),

        /**
         * X-Frame-Options: Prevents clickjacking attacks.
         * Options: 'DENY', 'SAMEORIGIN', 'ALLOW-FROM uri'
         */
        'x_frame_options' => env('X_FRAME_OPTIONS', 'SAMEORIGIN'),

        /**
         * X-Content-Type-Options: Prevents MIME sniffing.
         */
        'x_content_type_options' => env('X_CONTENT_TYPE_OPTIONS', 'nosniff'),

        /**
         * X-XSS-Protection: Legacy XSS protection for older browsers.
         */
        'x_xss_protection' => env('X_XSS_PROTECTION', '1; mode=block'),

        /**
         * Referrer-Policy: Controls referrer information.
         */
        'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),

        /**
         * Permissions-Policy: Controls browser features.
         */
        'permissions_policy' => [
            'geolocation' => '()',
            'microphone' => '()',
            'camera' => '()',
        ],

        /**
         * HSTS (HTTP Strict Transport Security) settings.
         */
        'hsts_enabled' => env('HSTS_ENABLED', true),
        'hsts_max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
        'hsts_include_subdomains' => env('HSTS_INCLUDE_SUBDOMAINS', true),
        'hsts_preload' => env('HSTS_PRELOAD', false),

        /**
         * Content-Security-Policy (optional, can be very restrictive).
         * Leave null to disable. Example: "default-src 'self'; script-src 'self' 'unsafe-inline'"
         */
        'content_security_policy' => env('CSP_POLICY', null),

        /**
         * Resource Isolation Policy (Fetch Metadata).
         * Blocks cross-site requests that aren't top-level navigations.
         */
        'fetch_metadata' => [
            'enabled' => env('SECURITY_FETCH_METADATA', false), // Default disabled
        ],
    ],

    /**
     * Cache Security Configuration
     */
    'cache' => [
        /**
         * Allowed classes for unserialization in cache.
         * Add your application classes here if you need to cache objects.
         * Empty array means no classes allowed (safest, uses JSON for simple data).
         */
        'allowed_classes' => [
            // 'App\\Models\\User',
            // 'App\\DTOs\\SomeDTO',
        ],
    ],
];
