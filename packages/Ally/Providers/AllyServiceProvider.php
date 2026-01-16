<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AllyServiceProvider registers the Ally package services.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Providers;

use Ally\Events\LowCreditEvent;
use Ally\Listeners\RewardPartnerReferral;
use Ally\Listeners\SendLowCreditNotificationListener;
use Ally\Services\AllyManagerService;
use Ally\Services\AnalyticsManagerService;
use Core\Event;
use Core\Services\ServiceProvider;
use Refer\Events\ReferralCompletedEvent;

class AllyServiceProvider extends ServiceProvider
{
    /**
     * Register the package services.
     */
    public function register(): void
    {
        $this->container->singleton(AllyManagerService::class);
        $this->container->singleton(AnalyticsManagerService::class);
    }

    public function boot(): void
    {
        Event::listen(LowCreditEvent::class, SendLowCreditNotificationListener::class);
        Event::listen(ReferralCompletedEvent::class, RewardPartnerReferral::class);
    }
}
