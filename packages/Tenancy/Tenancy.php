<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tenancy Facade provides a clean static interface for the multi-tenancy system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy;

use Tenancy\Models\Tenant;

class Tenancy
{
    public static function identify(?string $subdomain): ?Tenant
    {
        return resolve(TenantManager::class)->identifyBySubdomain($subdomain);
    }

    /**
     * Set tenant context.
     */
    public static function setContext(Tenant $tenant): void
    {
        resolve(TenantManager::class)->setContext($tenant);
    }

    public static function current(): ?Tenant
    {
        return resolve(TenantManager::class)->current();
    }

    public static function reset(): void
    {
        resolve(TenantManager::class)->reset();
    }

    public static function isEnabled(): bool
    {
        return resolve(TenantManager::class)->isEnabled();
    }

    /**
     * Test tenant database connection.
     */
    public static function testConnection(Tenant $tenant): bool
    {
        return resolve(TenantManager::class)->testConnection($tenant);
    }

    /**
     * Invalidate tenant cache.
     */
    public static function invalidateCache(Tenant $tenant): void
    {
        resolve(TenantManager::class)->invalidateCache($tenant);
    }
}
