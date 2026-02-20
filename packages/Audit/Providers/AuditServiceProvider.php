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

use App\Models\User;
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
        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        if (!config('audit.enabled', true)) {
            return;
        }

        $container = $this->container;
        $auditService = $container->get(AuditManagerService::class);

        if (config('audit.events.created', true)) {
            User::created(function ($user) use ($auditService) {
                $auditService->logModelEvent($user, 'created', [], $user->attributes);
            });
        }

        if (config('audit.events.updated', true)) {
            User::updated(function ($user) use ($auditService) {
                $auditService->logModelEvent($user, 'updated', $user->getOriginal(), $user->attributes);
            });
        }

        if (config('audit.events.deleted', true)) {
            User::deleted(function ($user) use ($auditService) {
                $auditService->logModelEvent($user, 'deleted', $user->attributes, []);
            });
        }
    }
}
