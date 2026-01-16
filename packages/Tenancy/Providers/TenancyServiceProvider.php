<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for converting tenant paths.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Providers;

use Core\Services\ServiceProvider;
use Tenancy\TenantManager;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(TenantManager::class);
    }

    public function boot(): void
    {
        // Helper is autoloaded via composer.json
    }
}
