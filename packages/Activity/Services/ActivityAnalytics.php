<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Activity analytics service for tracking trends and patterns.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Services;

use Activity\Models\Activity;
use Database\Query\Builder;
use Helpers\DateTimeHelper;

class ActivityAnalytics
{
    public function getTrends(string $period = 'daily', int $limit = 30): array
    {
        $query = static::queryWithDate($period)
            ->selectRaw('COUNT(*) as count')
            ->orderBy('date', 'desc')
            ->limit($limit);

        return array_reverse($query->get());
    }

    public function getActionBreakdown(string $period = 'daily', int $limit = 30): array
    {
        return static::queryWithDate($period)
            ->select('description')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('date', 'description')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->cache(config('activity.cache.trends', 1800))
            ->get();
    }

    /**
     * Get retention metrics (DAU/MAU and stickiness).
     */
    public function getRetentionStats(): array
    {
        // Simple 1 hour cache for expensive retention queries
        $cacheKey = 'activity.retention_stats';
        $ttl = config('activity.cache.trends', 3600);

        // Check if cache exists (pseudo-implementation as we are in a service method)
        // In a real scenario, we'd wrap this or use a cacheable command, but for now
        // we'll leave it direct as the Query Builder cache() method is per-query.
        // Since this method runs two queries, we can't easily use the builder's ->cache()
        // on the result set combinator without a Cache facade.
        // For simplicity in this step, we will proceed without caching this complex method
        // or refactor it later if Cache facade is available.
        // Actually, let's skip adding cache to this multi-query method for now to avoid complexity without a Cache service instance.

        $now = DateTimeHelper::now();
        $thirtyDaysAgo = $now->copy()->subDays(30);

        $dau = Activity::query()
            ->where('created_at', '>=', $now->copy()->startOfDay())
            ->distinct()
            ->count('user_id');

        $mau = Activity::query()
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->distinct()
            ->count('user_id');

        return [
            'dau' => $dau,
            'mau' => $mau,
            'stickiness' => $mau > 0 ? round(($dau / $mau) * 100, 2) . '%' : '0%',
        ];
    }

    /**
     * Track conversion drops across a sequence of tags.
     * Example: ['onboard_start', 'onboard_profile', 'onboard_complete']
     */
    public function getFunnel(array $steps): array
    {
        if (empty($steps)) {
            return [];
        }

        // Build a single query to get all distinct counts for the funnel steps
        $query = Activity::query()->whereIn('tag', $steps);

        foreach ($steps as $index => $tag) {
            $query->selectRaw("COUNT(DISTINCT CASE WHEN tag = ? THEN user_id END) as step_{$index}", [$tag]);
        }

        $counts = $query->first();
        $results = [];
        $totalUsers = (int) ($counts->step_0 ?? 0);

        foreach ($steps as $index => $tag) {
            $count = (int) ($counts->{"step_{$index}"} ?? 0);
            $results[] = [
                'step' => $tag,
                'users' => $count,
                'conversion' => $totalUsers > 0 ? round(($count / $totalUsers) * 100, 2) . '%' : '0%',
                'drop_off' => $index > 0 ? (isset($results[$index - 1]) && $results[$index - 1]['users'] > 0
                    ? round((1 - ($count / $results[$index - 1]['users'])) * 100, 2) . '%'
                    : '0%') : '0%'
            ];
        }

        return $results;
    }

    /**
     * Map the activity flow (User Journey) for a specific session.
     */
    public function getSessionFlow(string $sessionId): array
    {
        return Activity::query()
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->cache(config('activity.cache.behavior', 900))
            ->get();
    }

    public function getChannelStats(): array
    {
        return Activity::query()
            ->select('channel')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('channel')
            ->orderBy('count', 'desc')
            ->cache(config('activity.cache.channel_stats', 3600))
            ->get()->all();
    }

    public function getTopUsers(int $limit = 10): array
    {
        return Activity::query()
            ->select('user_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->cache(config('activity.cache.users', 3600))
            ->get()->all();
    }

    public function getTagStats(): array
    {
        return Activity::query()
            ->select('tag')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('tag')
            ->orderBy('count', 'desc')
            ->cache(config('activity.cache.trends', 1800))
            ->get()->all();
    }

    public function getLevelStats(): array
    {
        return Activity::query()
            ->select('level')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('count', 'desc')
            ->cache(config('activity.cache.trends', 1800))
            ->get()->all();
    }

    public function getSubjectStats(string $type, int $limit = 10): array
    {
        return Activity::query()
            ->where('subject_type', $type)
            ->select('subject_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('subject_id')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->cache(config('activity.cache.trends', 1800))
            ->get()->all();
    }

    public function getRecent(int $limit = 50): array
    {
        return Activity::query()
            ->latest()
            ->limit($limit)
            ->get()->all();
    }

    public function getUserBehavior(int $userId): array
    {
        // This is a composite result, direct caching of the array would require a Cache service.
        // We will cache the individual sub-queries where possible.

        $ttl = config('activity.cache.behavior', 900);

        return [
            'total_actions' => Activity::query()->where('user_id', $userId)->cache($ttl)->count(),
            'most_frequent_action' => Activity::query()
                ->where('user_id', $userId)
                ->select('description')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('description')
                ->orderBy('count', 'desc')
                ->cache($ttl)
                ->first(),
            'recent_activity' => Activity::query()
                ->where('user_id', $userId)
                ->latest()
                ->limit(5)
                ->get()->all()
        ];
    }

    /**
     * Internal helper to standardize date grouping based on period.
     */
    protected static function queryWithDate(string $period): Builder
    {
        $query = Activity::query();

        if ($period === 'weekly') {
            // Weekly still needs strftime as we only store daily key
            $query->selectRaw("strftime('%Y-%W', created_at) as date")
                ->groupBy('date');
        } elseif ($period === 'monthly') {
            // Monthly uses substring of date_key (YYYY-MM)
            $query->selectRaw("SUBSTR(date_key, 1, 7) as date")
                ->groupBy('date');
        } else {
            // Daily uses the optimized date_key
            $query->select('date_key as date')
                ->groupBy('date_key');
        }

        return $query;
    }
}
