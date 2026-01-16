<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Guide Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Providers;

use Core\Services\ServiceProvider;
use Guide\Services\AnalyticsManagerService;
use Guide\Services\Builders\ArticleBuilder;
use Guide\Services\Builders\CategoryBuilder;
use Guide\Services\GuideManagerService;

class GuideServiceProvider extends ServiceProvider
{
    /**
     * Register the package services.
     */
    public function register(): void
    {
        $this->container->singleton(GuideManagerService::class);
        $this->container->singleton(AnalyticsManagerService::class);

        $this->container->bind(ArticleBuilder::class, function () {
            return new ArticleBuilder();
        });

        $this->container->bind(CategoryBuilder::class, function () {
            return new CategoryBuilder();
        });
    }

    public function boot(): void
    {
        // Boot logic if any
    }
}
