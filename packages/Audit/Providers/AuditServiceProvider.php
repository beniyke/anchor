<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Audit package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Providers;

use Audit\Services\AuditManagerService;
use Core\Services\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(AuditManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
