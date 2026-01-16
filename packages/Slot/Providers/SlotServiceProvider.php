<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Slot system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Providers;

use Core\Event;
use Core\Services\ServiceProvider;
use Slot\Events\BookingReminderEvent;
use Slot\Interfaces\SlotServiceInterface;
use Slot\Listeners\SendBookingReminderNotificationListener;
use Slot\Services\SlotService;
use Slot\Slot;
use Slot\SlotManager;

class SlotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(SlotServiceInterface::class, SlotService::class);
        $this->container->singleton(SlotManager::class, SlotManager::class);
        $this->container->singleton(Slot::class);
    }

    public function boot(): void
    {
        Event::listen(BookingReminderEvent::class, SendBookingReminderNotificationListener::class);
    }
}
