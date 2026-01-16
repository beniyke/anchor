<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Metric Analytics Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services;

use Database\DB;
use Metric\Models\Goal;
use Metric\Models\Review;

class MetricAnalyticsService
{
    public function goalOverview(): array
    {
        return [
            'total_goals' => Goal::count(),
            'average_progress' => Goal::avg('progress') ?? 0.0,
            'completed_goals' => Goal::where('status', 'completed')->count(),
            'overdue_goals' => Goal::where('status', 'overdue')->count(),
        ];
    }

    public function cycleBenchmark(int $cycleId): float
    {
        return Review::where('metric_review_cycle_id', $cycleId)
            ->avg('rating') ?? 0.0;
    }

    public function topPerformers(int $limit = 5): array
    {
        return Review::select('user_id', DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('user_id')
            ->orderBy('avg_rating', 'desc')
            ->limit($limit)
            ->with('user')
            ->get()
            ->toArray();
    }
}
