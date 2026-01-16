<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link analytics service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Services;

use Database\DB;
use Helpers\DateTimeHelper;

class LinkAnalyticsService
{
    public function getTopResources(int $days = 30, int $limit = 10): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('link_usage')
            ->join('link', 'link_usage.link_id', '=', 'link.id')
            ->selectRaw('link.linkable_type, link.linkable_id, COUNT(*) as access_count')
            ->where('link_usage.used_at', '>=', $since)
            ->whereNotNull('link.linkable_type')
            ->groupBy('link.linkable_type', 'link.linkable_id')
            ->orderByDesc('access_count')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getUsageTrends(int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('link_usage')
            ->selectRaw('DATE(used_at) as date, COUNT(*) as access_count')
            ->where('used_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->all();
    }

    public function getCreationTrends(int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('link')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as created_count')
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->all();
    }

    public function getExpirationMetrics(): array
    {
        $now = DateTimeHelper::now()->toDateTimeString();

        $total = DB::table('link')->count();

        $active = DB::table('link')
            ->whereNull('revoked_at')
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $now);
            })
            ->count();

        $expired = DB::table('link')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->count();

        $revoked = DB::table('link')
            ->whereNotNull('revoked_at')
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'expired' => $expired,
            'revoked' => $revoked,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100, 1) : 0,
        ];
    }

    public function getTopCreators(int $limit = 10): array
    {
        return DB::table('link')
            ->selectRaw('created_by, COUNT(*) as link_count')
            ->whereNotNull('created_by')
            ->groupBy('created_by')
            ->orderByDesc('link_count')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getScopeDistribution(): array
    {
        $links = DB::table('link')
            ->select('scopes')
            ->whereNotNull('scopes')
            ->get();

        $scopeCounts = [];

        foreach ($links as $link) {
            $scopes = json_decode($link->scopes, true) ?? [];
            foreach ($scopes as $scope) {
                $scopeCounts[$scope] = ($scopeCounts[$scope] ?? 0) + 1;
            }
        }

        arsort($scopeCounts);

        return $scopeCounts;
    }
}
