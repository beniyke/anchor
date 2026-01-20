<?php

declare(strict_types=1);

/**
 * Email Validation Configuration
 *
 * Configure email validation behavior for the Anchor framework
 */

return [
    /**
     * Disposable Email Domains
     *
     * Configuration for disposable/temporary email detection
     */
    'disposable_domains' => [
        // Add custom disposable domains here
        'custom' => [
            // 'example-disposable.com',
        ],
    ],

    /**
     * Role-Based Email Accounts
     *
     * Configuration for role-based email detection
     */
    'role_accounts' => [
        // Add custom role prefixes here
        'custom' => [
            // 'customrole',
        ],
    ],

    /**
     * Global Domain Whitelist
     *
     * Domains in this list will always be allowed (optional)
     * Supports wildcard patterns: *.example.com
     */
    'global_whitelist' => [
        // 'trusted-company.com',
        // '*.verified-domain.com',
    ],

    /**
     * Global Domain Blacklist
     *
     * Domains in this list will always be blocked (optional)
     * Supports wildcard patterns: *.example.com
     */
    'global_blacklist' => [
        // 'blocked-domain.com',
        // '*.spam-domain.com',
    ],

    /**
     * DNS Validation Settings
     *
     * Configure MX record validation behavior
     */
    'dns_validation' => [
        // Enable/disable DNS MX record checking
        'enabled' => true,

        // Timeout for DNS lookups (seconds)
        'timeout' => 5,

        // Graceful fallback if DNS check fails
        // If true, allows email if DNS check times out or fails
        'graceful_fallback' => true,
    ],

    /**
     * SMTP Verification Settings
     *
     * Verify if email mailbox actually exists by connecting to mail server
     * WARNING: This is slow and may be blocked by some mail servers
     * Use sparingly and consider caching results
     */
    'smtp_verification' => [
        // Enable/disable SMTP mailbox verification
        'enabled' => false, // Disabled by default

        // Timeout for SMTP connection (seconds)
        'timeout' => 10,

        // Enable debug output (for development only)
        'debug' => false,

        // Graceful fallback if SMTP check fails
        // If true, allows email if SMTP check times out or fails
        'graceful_fallback' => true,

        // Caching configuration
        'cache' => [
            'enabled' => true, // Enable caching by default
            'duration' => 86400, // Cache for 24 hours (in seconds)
            'key_prefix' => 'smtp_verify_', // Cache key prefix
        ],

        // Domains to exclude from SMTP verification
        // Useful for large providers that may block verification attempts
        'exclude_domains' => [
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'outlook.com',
            'live.com',
            'icloud.com',
            'protonmail.com',
        ],
    ],

    /**
     * Logging
     *
     * Enable logging for blocked emails (useful for monitoring)
     */
    'logging' => [
        'enabled' => false,
        'log_disposable' => true,
        'log_role_accounts' => true,
        'log_blocked_domains' => true,
        'log_smtp_verification' => true,
    ],
];
