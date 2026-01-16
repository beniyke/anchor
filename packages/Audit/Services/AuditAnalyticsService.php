<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Audit package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Services;

use Audit\Models\AuditLog;
use Helpers\DateTimeHelper;

class AuditAnalyticsService
{
    public function getOverview(): array
    {
        $today = DateTimeHelper::now()->format('Y-m-d');
        $thisWeek = DateTimeHelper::now()->subDays(7)->format('Y-m-d');
        $thisMonth = DateTimeHelper::now()->subDays(30)->format('Y-m-d');

        return [
            'total_events' => AuditLog::count(),
            'events_today' => AuditLog::where('created_at', '>=', $today)->count(),
            'events_this_week' => AuditLog::where('created_at', '>=', $thisWeek)->count(),
            'events_this_month' => AuditLog::where('created_at', '>=', $thisMonth)->count(),
            'unique_users' => AuditLog::distinct('user_id')->count('user_id'),
        ];
    }

    public function getByEvent(): array
    {
        return AuditLog::selectRaw('event, COUNT(*) as event_count')
            ->groupBy('event')
            ->orderBy('event_count', 'desc')
            ->get()
            ->all();
    }

    public function getByModel(): array
    {
        return AuditLog::selectRaw('auditable_type, COUNT(*) as event_count')
            ->whereNotNull('auditable_type')
            ->groupBy('auditable_type')
            ->orderBy('event_count', 'desc')
            ->get()
            ->all();
    }

    public function getMostActiveUsers(int $limit = 10): array
    {
        return AuditLog::selectRaw('user_id, COUNT(*) as action_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('action_count', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getHourlyDistribution(): array
    {
        $result = array_fill(0, 24, 0);

        $hourlyData = AuditLog::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->get();

        foreach ($hourlyData as $data) {
            $result[(int) $data->hour] = $data->count;
        }

        return $result;
    }

    public function getDailyTrends(int $days = 30): array
    {
        $trends = [];
        $startDate = DateTimeHelper::now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i)->format('Y-m-d');
            $nextDate = $startDate->addDays($i + 1)->format('Y-m-d');

            $trends[] = [
                'date' => $date,
                'events' => AuditLog::where('created_at', '>=', $date)
                    ->where('created_at', '<', $nextDate)
                    ->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get security-related events.
     */
    public function getSecurityEvents(int $limit = 50): array
    {
        $securityEvents = ['login', 'logout', 'failed_login', 'password_changed', 'permission_denied', 'deleted'];

        return AuditLog::whereIn('event', $securityEvents)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getSuspiciousActivity(): array
    {
        $yesterday = DateTimeHelper::now()->subDays(1)->format('Y-m-d');

        // IPs with multiple failed logins
        $suspiciousIps = AuditLog::selectRaw('user_ip, COUNT(*) as attempt_count')
            ->where('event', 'failed_login')
            ->where('created_at', '>=', $yesterday)
            ->groupBy('user_ip')
            ->having('attempt_count', '>=', 5)
            ->get()
            ->all();

        return [
            'suspicious_ips' => $suspiciousIps,
            'failed_logins_24h' => AuditLog::where('event', 'failed_login')
                ->where('created_at', '>=', $yesterday)
                ->count(),
        ];
    }
}
