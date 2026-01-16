<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Managing tenant identification, context, and isolation.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy;

use Core\Services\ConfigServiceInterface;
use Helpers\File\Contracts\CacheInterface;
use Tenancy\Exceptions\TenantException;
use Tenancy\Models\Tenant;
use Tenancy\Strategies\SeparateDatabaseStrategy;

class TenantManager
{
    private ?Tenant $currentTenant = null;

    private SeparateDatabaseStrategy $strategy;

    private array $memoryCache = [];

    private ConfigServiceInterface $config;

    private CacheInterface $cacheStore;

    public function __construct(ConfigServiceInterface $config, CacheInterface $cache)
    {
        $this->strategy = new SeparateDatabaseStrategy();
        $this->config = $config;
        $this->cacheStore = $cache->withPath('tenants');
    }

    public function identifyBySubdomain(?string $subdomain): ?Tenant
    {
        // Check if multi-tenancy is enabled
        if (!$this->isEnabled()) {
            return $this->getDefaultTenant();
        }

        // No subdomain provided
        if (!$subdomain) {
            return $this->getDefaultTenant();
        }

        // Sanitize subdomain
        $subdomain = $this->sanitizeSubdomain($subdomain);

        // Validate subdomain format
        if (!Tenant::isValidSubdomain($subdomain)) {
            throw TenantException::invalidSubdomain($subdomain);
        }

        if (isset($this->memoryCache[$subdomain])) {
            return $this->memoryCache[$subdomain];
        }

        // Try persistent cache
        $cacheKey = $this->getCacheKey($subdomain);
        $tenant = $this->cacheStore->read($cacheKey);

        if (!$tenant) {
            // Query database
            $tenant = Tenant::where('subdomain', $subdomain)->first();

            if ($tenant) {
                // Cache for configured TTL
                $ttl = $this->config->get('tenancy.cache.ttl', 3600);
                $this->cacheStore->write($cacheKey, $tenant, $ttl);
            }
        }

        // Store in memory cache
        if ($tenant) {
            $this->memoryCache[$subdomain] = $tenant;
        }

        return $tenant;
    }

    public function setContext(Tenant $tenant): void
    {
        // Validate tenant status
        if (!$tenant->isActive()) {
            if ($tenant->isSuspended()) {
                throw TenantException::suspended($tenant->subdomain);
            }

            throw TenantException::expired($tenant->subdomain);
        }

        // Prevent context switching mid-request if already set
        if ($this->currentTenant && $this->currentTenant->id !== $tenant->id) {
            throw new TenantException('Cannot switch tenant context mid-request');
        }

        $this->currentTenant = $tenant;

        $this->updateActivityAsync($tenant);

        // Apply database isolation strategy
        $this->strategy->setTenantContext($tenant);

        // Load tenant configuration
        $this->loadTenantConfig($tenant);
    }

    public function current(): ?Tenant
    {
        return $this->currentTenant;
    }

    public function reset(): void
    {
        $this->currentTenant = null;
        $this->strategy->resetContext();
    }

    public function isEnabled(): bool
    {
        return $this->config->get('tenancy.enabled', false);
    }

    public function testConnection(Tenant $tenant): bool
    {
        return $this->strategy->testConnection($tenant);
    }

    public function invalidateCache(Tenant $tenant): void
    {
        $cacheKey = $this->getCacheKey($tenant->subdomain);
        $this->cacheStore->delete($cacheKey);
        unset($this->memoryCache[$tenant->subdomain]);
    }

    private function loadTenantConfig(Tenant $tenant): void
    {
        $config = $tenant->config ?? [];

        foreach ($config as $key => $value) {
            $this->config->set("tenancy.runtime.{$key}", $value);
        }

        // Set tenant-specific limits
        $this->config->set('tenancy.runtime.max_users', $tenant->max_users);
        $this->config->set('tenancy.runtime.max_storage_mb', $tenant->max_storage_mb);
        $this->config->set('tenancy.runtime.plan', $tenant->plan);
    }

    private function getDefaultTenant(): ?Tenant
    {
        $defaultSubdomain = $this->config->get('tenancy.default_subdomain');

        if ($defaultSubdomain) {
            return Tenant::where('subdomain', $defaultSubdomain)->first();
        }

        return Tenant::where('is_default', true)->first();
    }

    private function sanitizeSubdomain(string $subdomain): string
    {
        // Convert to lowercase
        $subdomain = strtolower($subdomain);

        // Remove any non-alphanumeric characters except hyphens
        $subdomain = preg_replace('/[^a-z0-9-]/', '', $subdomain);

        // Trim hyphens from start and end
        $subdomain = trim($subdomain, '-');

        return $subdomain;
    }

    private function getCacheKey(string $subdomain): string
    {
        $prefix = $this->config->get('tenancy.cache.key_prefix', 'tenant:');

        return "{$prefix}subdomain:{$subdomain}";
    }

    private function updateActivityAsync(Tenant $tenant): void
    {
        // Update activity directly (async can be added later via queue)
        $tenant->touchActivity();
    }
}
