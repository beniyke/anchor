<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service for managing notifications within the dedicated Notification package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Services;

use App\Models\User;
use Database\DB;
use Database\Pagination\Paginator;
use Helpers\Data\Data;
use Notification\Models\Notification;
use Throwable;

class NotificationManagerService
{
    private const DEFAULT_PER_PAGE = 10;

    public function listNotifications(User $user, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $notifications = Notification::latestForUser($user->id)
            ->paginate($perPage, $page);

        if ($user->hasUnreadNotifications()) {
            $this->markAllAsRead($user);
        }

        return $notifications;
    }

    public function notifyUser(Data $payload): ?Notification
    {
        static $hasTable = null;

        if ($hasTable === null) {
            try {
                $hasTable = DB::connection()->tableExists(Notification::TABLE);
            } catch (Throwable $e) {
                $hasTable = false;
            }
        }

        if (! $hasTable) {
            return null;
        }

        return Notification::create($payload->data());
    }

    public function clearUserNotifications(User $user): bool
    {
        $deletedCount = Notification::deleteAll($user->id);

        return $deletedCount > 0;
    }

    public function notifyAll(Data $payload): int
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $data = $payload->data();
            $data['user_id'] = $user->id;

            Notification::create($data);
            $count++;
        }

        return $count;
    }

    public function markAllAsRead(User $user): void
    {
        Notification::markAllAsRead($user->id);
    }

    /**
     * Prune notifications older than a specific number of days.
     */
    public function prune(int $days, bool $pruneUnread): int
    {
        return Notification::prune($days, $pruneUnread);
    }
}
