<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Hub package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Providers;

use Core\Event;
use Core\Services\ServiceProvider;
use Hub\Events\MessagePostedEvent;
use Hub\Events\ReminderDueEvent;
use Hub\Events\UserMentionedEvent;
use Hub\Listeners\SendMentionNotificationListener;
use Hub\Listeners\SendMessagePostedNotificationListener;
use Hub\Listeners\SendReminderNotificationListener;
use Hub\Services\HubAnalyticsService;
use Hub\Services\HubManagerService;

class HubServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(HubManagerService::class);
        $this->container->singleton(HubAnalyticsService::class);
    }

    public function boot(): void
    {
        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        Event::listen(UserMentionedEvent::class, SendMentionNotificationListener::class);
        Event::listen(MessagePostedEvent::class, SendMessagePostedNotificationListener::class);
        Event::listen(ReminderDueEvent::class, SendReminderNotificationListener::class);
    }
}
