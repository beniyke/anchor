<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Proof Service Provider.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Providers;

use Core\Services\ServiceProvider;
use Proof\Services\AnalyticsManagerService;
use Proof\Services\Builders\CaseStudyBuilder;
use Proof\Services\Builders\TestimonialBuilder;
use Proof\Services\ProofManagerService;

class ProofServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        $this->container->singleton(ProofManagerService::class);
        $this->container->singleton(AnalyticsManagerService::class);

        $this->container->bind(TestimonialBuilder::class, function () {
            return new TestimonialBuilder();
        });

        $this->container->bind(CaseStudyBuilder::class, function () {
            return new CaseStudyBuilder();
        });
    }

    public function boot(): void
    {
        // Add boot logic if needed
    }
}
