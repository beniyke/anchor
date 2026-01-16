<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Pulse Analytics Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Pulse\Models\Channel;
use Pulse\Models\Post;
use Pulse\Models\Thread;

class PulseAnalyticsService
{
    public function totalThreads(): int
    {
        return Thread::count();
    }

    public function totalPosts(): int
    {
        return Post::count();
    }

    public function dailyActivity(int $days = 30): array
    {
        return Post::select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        ])
            ->where('created_at', '>=', DateTimeHelper::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function popularThreads(int $limit = 5): array
    {
        return Thread::orderBy('view_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function channelGrowth(): array
    {
        return Channel::withCount('threads')
            ->get()
            ->mapWithKeys(fn ($channel) => [$channel->name => $channel->threads_count]);
    }
}
