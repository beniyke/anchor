<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for the Scribe package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Scribe\Models\Post;

class ScribeAnalyticsService
{
    public function getPostTrends(Post $post, int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('scribe_event')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('scribe_post_id', $post->id)
            ->where('event_type', 'view')
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    public function getTopPosts(int $limit = 10, int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('scribe_event')
            ->selectRaw('scribe_post_id, COUNT(*) as view_count')
            ->where('event_type', 'view')
            ->where('created_at', '>=', $since)
            ->groupBy('scribe_post_id')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTotalViews(Post $post): int
    {
        return DB::table('scribe_event')
            ->where('scribe_post_id', $post->id)
            ->where('event_type', 'view')
            ->count();
    }
}
