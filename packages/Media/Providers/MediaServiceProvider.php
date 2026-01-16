<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Media package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Providers;

use Core\Services\ServiceProvider;
use Media\Services\MediaManagerService;

class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(MediaManagerService::class);
    }

    public function boot(): void
    {
        // Any boot logic
    }
}
