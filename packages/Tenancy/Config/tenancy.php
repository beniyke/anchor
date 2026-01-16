<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tenancy configuration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    // Enable/disable multi-tenancy
    'enabled' => env('MULTI_TENANT_ENABLED', true),

    // Isolation strategy: 'separate_db', 'separate_schema', 'shared'
    'isolation_strategy' => 'separate_db',

    // Central domain (without subdomain)
    'central_domain' => env('CENTRAL_DOMAIN', 'localhost'),

    // Excluded subdomains (reserved for system use)
    'excluded_subdomains' => [
        'www',
        'api',
        'admin',
        'app',
        'mail',
        'cdn',
        'static',
        'assets',
    ],

    // Excluded paths (skip tenant identification)
    'excluded_paths' => [
        '/health',
        '/status',
        '/_debug',
        '/metrics',
    ],

    // Default tenant subdomain (for single-tenant mode)
    'default_subdomain' => env('DEFAULT_SUBDOMAIN', null),

    // Tenant database configuration
    'database' => [
        'driver' => env('TENANT_DB_DRIVER', 'mysql'),
        'default_host' => env('TENANT_DB_HOST', '127.0.0.1'),
        'default_port' => env('TENANT_DB_PORT', '3306'),
        'default_charset' => 'utf8mb4',
        'default_collation' => 'utf8mb4_unicode_ci',
        'prefix_pattern' => env('TENANT_DB_PREFIX', 'tenant_'), // Database naming pattern
        'migrations_path' => Helpers\File\Paths::storagePath('database/migrations'),
    ],

    // Tenant-specific feature flags
    'features' => [
        'custom_branding' => true,
        'custom_domain' => false,
        'api_access' => true,
        'webhooks' => true,
    ],

    // Cache configuration
    'cache' => [
        'ttl' => env('TENANT_CACHE_TTL', 3600), // Cache tenant data for 1 hour
        'key_prefix' => 'tenant:',
    ],
];
