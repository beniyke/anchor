<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Notification package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Providers;

use App\Models\User;
use Core\Services\ServiceProvider;
use Notification\Channels\Adapters\InAppAdapter;
use Notification\Channels\Adapters\Interfaces\InAppAdapterInterface;
use Notification\Channels\InAppChannel;
use Notification\Models\Notification;
use Notification\Services\NotificationAnalytics;
use Notification\Services\NotificationManagerService;
use Notify\NotificationManager;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(NotificationManagerService::class, NotificationManagerService::class);
        $this->container->singleton(NotificationAnalytics::class, NotificationAnalytics::class);
        $this->container->singleton(InAppAdapterInterface::class, InAppAdapter::class);
        $this->container->singleton(InAppChannel::class);

        // Register the channel to the core Notify manager
        $this->container->extend(NotificationManager::class, function ($manager, $container) {
            $manager->registerChannel('in-app', $container->make(InAppChannel::class));

            return $manager;
        });
    }

    public function boot(): void
    {
        User::macro('unreadNotificationsData', function (int $limit = 5) {
            return [
                'notifications' => Notification::unreadForUser($this->id, $limit),
                'count' => Notification::unreadCountForUser($this->id),
            ];
        });

        User::macro('hasUnreadNotifications', function () {
            $totalUnread = Notification::unreadCountForUser($this->id);

            return $totalUnread > 0;
        });
    }
}
