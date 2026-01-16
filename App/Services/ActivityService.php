<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Views\Models\ActivityViewModel;
use Database\Pagination\Paginator;
use Helpers\DateTimeHelper;

class ActivityService
{
    private const DEFAULT_PER_PAGE = 20;

    public function listUserActivities(User $user, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $activities = Activity::latestForUser($user->id)
            ->paginate($perPage, $page);

        $activities->setItems(ActivityViewModel::collection($activities->items()));

        return $activities;
    }

    public function listRecentActivities(int $days = 7, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $query = Activity::query();

        if ($days === 0) {
            $query->today();
        } elseif ($days > 0) {
            $query->recent($days);
        }

        $query->with('user');

        return $query
            ->latest()
            ->paginate($perPage, $page);
    }

    public function getSummary(Activity $activity): string
    {
        $timeAgo = DateTimeHelper::timeAgo($activity->created_at);

        return $activity->formatSummary($timeAgo);
    }
}
