<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification analytics service for tracking delivery and engagement metrics.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Services;

use Database\Query\Builder;
use Notification\Models\Notification;

class NotificationAnalytics
{
    public function getTrends(string $period = 'daily', int $limit = 30): array
    {
        $query = static::queryWithDate($period)
            ->selectRaw('COUNT(*) as count')
            ->orderBy('date', 'desc')
            ->limit($limit);

        return array_reverse($query->get());
    }

    /**
     * Get distribution of notifications by label/type.
     */
    public function getLabelBreakdown(): array
    {
        return Notification::query()
            ->select('label')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('label')
            ->orderBy('count', 'desc')
            ->cache(3600)
            ->get()->all();
    }

    public function getReadRateStats(): array
    {
        $total = Notification::query()->count();
        if ($total === 0) {
            return ['read' => 0, 'unread' => 0, 'percentage' => '0%'];
        }

        $read = Notification::query()->where('is_read', 1)->count();
        $unread = $total - $read;

        return [
            'total' => $total,
            'read' => $read,
            'unread' => $unread,
            'read_rate' => round(($read / $total) * 100, 2) . '%',
        ];
    }

    /**
     * Identify users receiving the most notifications.
     */
    public function getTopNotifiedUsers(int $limit = 10): array
    {
        return Notification::query()
            ->select('user_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->cache(3600)
            ->get()->all();
    }

    /**
     * Internal helper to standardize date grouping based on period.
     */
    protected static function queryWithDate(string $period): Builder
    {
        $query = Notification::query();

        // Optimized for SQLite/MySQL depending on the driver,
        // using the standard table format for now.
        if ($period === 'weekly') {
            $query->selectRaw("strftime('%Y-%W', created_at) as date")
                ->groupBy('date');
        } elseif ($period === 'monthly') {
            $query->selectRaw("strftime('%Y-%m', created_at) as date")
                ->groupBy('date');
        } else {
            $query->selectRaw("date(created_at) as date")
                ->groupBy('date');
        }

        return $query;
    }
}
