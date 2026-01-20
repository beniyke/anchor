<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Views\Models\NotificationViewModel;
use Database\Pagination\Paginator;
use Helpers\Data;

class NotificationService
{
    private const DEFAULT_PER_PAGE = 10;

    public function listNotifications(User $user, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $notifications = Notification::latestForUser($user->id)
            ->paginate($perPage, $page);

        $notifications->setItems(NotificationViewModel::collection($notifications->items()));

        if ($user->hasUnreadNotifications()) {
            $this->markAsViewedAndLog($user);
        }

        return $notifications;
    }

    public function notifyUser(Data $payload): ?Notification
    {
        return Notification::create($payload->data());
    }

    public function clearUserNotifications(User $user): bool
    {
        $deletedCount = Notification::deleteAll($user->id);

        if ($deletedCount > 0) {
            defer(function () use ($user, $deletedCount) {
                activity('cleared all {count} ' . inflect('notification', $deletedCount), ['count' => $deletedCount], $user->id);
            });
        }

        return $deletedCount > 0;
    }

    public function markAsViewedAndLog(User $user): void
    {
        defer(function () use ($user) {
            $updatedCount = Notification::markAllAsRead($user->id);
            activity('marked {count} ' . inflect('notification', $updatedCount) . ' as read', ['count' => $updatedCount], $user->id);
        });
    }
}
