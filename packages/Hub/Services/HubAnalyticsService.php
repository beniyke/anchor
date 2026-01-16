<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Hub analytics service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services;

use Database\DB;
use Helpers\DateTimeHelper;

class HubAnalyticsService
{
    public function getActiveThreads(int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('hub_thread')
            ->selectRaw('hub_thread.*, COUNT(hub_message.id) as message_count')
            ->leftJoin('hub_message', 'hub_thread.id', '=', 'hub_message.thread_id')
            ->where('hub_message.created_at', '>=', $since)
            ->groupBy('hub_thread.id')
            ->orderByDesc('message_count')
            ->limit(20)
            ->get()
            ->all();
    }

    public function getMessageTrends(int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('hub_message')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as message_count')
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->all();
    }

    public function getEngagementMetrics(): array
    {
        $totalThreads = DB::table('hub_thread')->count();
        $totalMessages = DB::table('hub_message')->count();
        $totalReminders = DB::table('hub_reminder')->count();

        $activeUsers = DB::table('hub_member')
            ->distinct()
            ->count('user_id');

        return [
            'total_threads' => $totalThreads,
            'total_messages' => $totalMessages,
            'total_reminders' => $totalReminders,
            'active_users' => $activeUsers,
            'avg_messages_per_thread' => $totalThreads > 0 ? round($totalMessages / $totalThreads, 1) : 0.0,
        ];
    }

    /**
     * Get most active users.
     */
    public function getTopContributors(int $limit = 10): array
    {
        return DB::table('hub_message')
            ->selectRaw('user_id, COUNT(*) as message_count')
            ->groupBy('user_id')
            ->orderByDesc('message_count')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getReactionDistribution(): array
    {
        return DB::table('hub_reaction')
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->orderByDesc('count')
            ->get()
            ->all();
    }

    public function getReminderMetrics(): array
    {
        $total = DB::table('hub_reminder')->count();
        $completed = DB::table('hub_reminder')->where('status', 'completed')->count();
        $pending = DB::table('hub_reminder')->where('status', 'pending')->count();
        $snoozed = DB::table('hub_reminder')->where('status', 'snoozed')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'snoozed' => $snoozed,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }
}
