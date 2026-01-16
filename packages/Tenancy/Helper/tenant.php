<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Helper functions for accessing tenant information.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

use Tenancy\Models\Tenant;
use Tenancy\TenantManager;

if (!function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        try {
            return container()->get('tenant');
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('tenant_manager')) {
    function tenant_manager(): ?TenantManager
    {
        try {
            return container()->get('tenant.manager');
        } catch (Throwable $e) {
            return null;
        }
    }
}

if (!function_exists('tenant_config')) {
    /**
     * Get tenant-specific configuration value
     */
    function tenant_config(string $key, $default = null)
    {
        $tenant = tenant();

        return $tenant ? $tenant->getConfig($key, $default) : $default;
    }
}

if (!function_exists('is_multi_tenant')) {
    /**
     * Check if multi-tenancy is enabled
     */
    function is_multi_tenant(): bool
    {
        return config('tenancy.enabled', false);
    }
}

if (!function_exists('tenant_db')) {
    function tenant_db(): string
    {
        return is_multi_tenant() ? 'tenant' : 'mysql';
    }
}

if (!function_exists('tenant_cache_key')) {
    /**
     * Generate cache key prefixed with tenant identifier
     */
    function tenant_cache_key(string $key): string
    {
        $tenant = tenant();
        $prefix = config('tenancy.cache.key_prefix', 'tenant:');

        return $tenant ? "{$prefix}{$tenant->id}:{$key}" : $key;
    }
}

if (!function_exists('tenant_url')) {
    /**
     * Generate URL for current tenant
     */
    function tenant_url(string $path = ''): string
    {
        $tenant = tenant();

        if (!$tenant) {
            return url($path);
        }

        $domain = config('tenancy.central_domain', 'localhost');
        $protocol = config('app.https', false) ? 'https' : 'http';

        return "{$protocol}://{$tenant->subdomain}.{$domain}/{$path}";
    }
}

if (!function_exists('within_limits')) {
    function within_limits(string $limit, int $current): bool
    {
        $tenant = tenant();

        if (!$tenant) {
            return true;
        }

        $max = match ($limit) {
            'users' => $tenant->max_users,
            'storage' => $tenant->max_storage_mb,
            default => PHP_INT_MAX,
        };

        return $current < $max;
    }
}
