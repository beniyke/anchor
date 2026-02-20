<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\LogActivityListener;
use Core\Event;
use Core\Events\KernelTerminateEvent;
use Core\Services\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<string, array<string>>
     */
    protected array $listen = [
        KernelTerminateEvent::class => [
            LogActivityListener::class,
        ],
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
