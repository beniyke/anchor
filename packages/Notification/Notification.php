<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification Facade
 * Provides a static interface for managing database notifications.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification;

use Helpers\Data\Data;
use Notification\Models\Notification as NotificationModel;
use Notification\Services\NotificationAnalytics;
use Notification\Services\NotificationManagerService;

class Notification
{
    protected static function manager(): NotificationManagerService
    {
        return resolve(NotificationManagerService::class);
    }

    public static function analytics(): NotificationAnalytics
    {
        return resolve(NotificationAnalytics::class);
    }

    /**
     * Send a notification to a specific user.
     */
    public static function notifyUser(array $data): ?NotificationModel
    {
        return static::manager()->notifyUser(Data::make($data));
    }

    /**
     * Send a notification to all users.
     */
    public static function notifyAll(array $data): int
    {
        return static::manager()->notifyAll(Data::make($data));
    }

    /**
     * Pass dynamic static calls to the manager service.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return static::manager()->$method(...$args);
    }
}
