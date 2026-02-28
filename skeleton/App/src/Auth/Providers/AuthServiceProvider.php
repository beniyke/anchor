<?php

declare(strict_types=1);

namespace App\Auth\Providers;

use App\Auth\Listeners\LoginFailedListener;
use App\Auth\Listeners\LoginListener;
use App\Auth\Listeners\LogoutListener;
use Core\Contracts\AuthServiceInterface;
use Core\Event;
use Core\Services\ServiceProvider;
use Security\Auth\AuthService;
use Security\Auth\Events\LoginEvent;
use Security\Auth\Events\LoginFailedEvent;
use Security\Auth\Events\LogoutEvent;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(AuthServiceInterface::class, AuthService::class);
        $this->container->alias(AuthServiceInterface::class, 'auth.service');
    }

    public function boot(): void
    {
        Event::listen(LoginEvent::class, LoginListener::class);
        Event::listen(LogoutEvent::class, LogoutListener::class);
        Event::listen(LoginFailedEvent::class, LoginFailedListener::class);
    }
}
