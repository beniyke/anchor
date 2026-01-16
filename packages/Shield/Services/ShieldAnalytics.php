<?php

declare(strict_types=1);

namespace Shield\Services;

use Audit\Audit;
use Database\Collections\ModelCollection;
use Database\DB;
use Helpers\DateTimeHelper;

class ShieldAnalytics
{
    public function overview(?string $driver = null, ?array $dateRange = null): array
    {
        if (! class_exists(Audit::class)) {
            return $this->emptyStats();
        }

        $query = Audit::queryBuilder()
            ->whereIn('event', ['captcha.verified', 'captcha.failed', 'captcha.error']);

        if ($driver) {
            $query->whereRaw("json_extract(metadata, '$.driver') = ?", [$driver]);
        }

        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }

        $total = $query->count();
        $verified = (clone $query)->where('event', 'captcha.verified')->count();
        $failed = (clone $query)->where('event', 'captcha.failed')->count();
        $errors = (clone $query)->where('event', 'captcha.error')->count();

        return [
            'total' => $total,
            'verified' => $verified,
            'failed' => $failed,
            'errors' => $errors,
            'success_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
        ];
    }

    public function trends(int $days = 30, ?string $driver = null): array
    {
        if (! class_exists(Audit::class)) {
            return [];
        }

        $query = Audit::queryBuilder()
            ->whereIn('event', ['captcha.verified', 'captcha.failed', 'captcha.error'])
            ->where('created_at', '>=', DateTimeHelper::now()->subDays($days));

        if ($driver) {
            $query->whereRaw("json_extract(metadata, '$.driver') = ?", [$driver]);
        }

        $data = $query->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw("SUM(CASE WHEN event = 'captcha.verified' THEN 1 ELSE 0 END) as verified"),
            DB::raw("SUM(CASE WHEN event = 'captcha.failed' THEN 1 ELSE 0 END) as failed"),
            DB::raw("SUM(CASE WHEN event = 'captcha.error' THEN 1 ELSE 0 END) as errors")
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data->map(function ($item) {
            return [
                'date' => $item->date,
                'verified' => (int) $item->verified,
                'failed' => (int) $item->failed,
                'errors' => (int) $item->errors,
                'total' => (int) $item->verified + (int) $item->failed + (int) $item->errors,
            ];
        });
    }

    /**
     * Get top IPs (potential abuse/bots).
     */
    public function topIps(int $limit = 10, ?string $driver = null): array
    {
        if (! class_exists(Audit::class)) {
            return [];
        }

        $query = Audit::queryBuilder()
            ->whereIn('event', ['captcha.verified', 'captcha.failed', 'captcha.error']);

        if ($driver) {
            $query->whereRaw("json_extract(metadata, '$.driver') = ?", [$driver]);
        }

        $data = $query->select(
            DB::raw("json_extract(metadata, '$.ip') as ip_address"),
            DB::raw('COUNT(*) as total_requests'),
            DB::raw("SUM(CASE WHEN event = 'captcha.failed' THEN 1 ELSE 0 END) as failed_attempts")
        )
            ->groupBy('ip_address')
            ->orderBy('total_requests', 'desc')
            ->limit($limit)
            ->get();

        return $data->map(function ($item) {
            return [
                'ip' => $item->ip_address ?? 'Unknown',
                'total' => (int) $item->total_requests,
                'failed' => (int) $item->failed_attempts,
                'fail_rate' => $item->total_requests > 0 ? round(($item->failed_attempts / $item->total_requests) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Compare performance across drivers.
     */
    public function driverPerformance(?array $dateRange = null): array
    {
        if (! class_exists(Audit::class)) {
            return [];
        }

        $query = Audit::queryBuilder()
            ->whereIn('event', ['captcha.verified', 'captcha.failed', 'captcha.error']);

        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }

        $data = $query->select(
            DB::raw("json_extract(metadata, '$.driver') as driver_name"),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN event = 'captcha.verified' THEN 1 ELSE 0 END) as verified")
        )
            ->groupBy('driver_name')
            ->get();

        return $data->map(function ($item) {
            return [
                'driver' => $item->driver_name ?? 'Unknown',
                'total' => (int) $item->total,
                'verified' => (int) $item->verified,
                'success_rate' => $item->total > 0 ? round(($item->verified / $item->total) * 100, 1) : 0,
            ];
        });
    }

    public function logs(?string $driver = null, int $limit = 50): ModelCollection|array
    {
        if (! class_exists(Audit::class)) {
            return new ModelCollection([]);
        }

        $query = Audit::queryBuilder()
            ->whereIn('event', ['captcha.verified', 'captcha.failed', 'captcha.error'])
            ->latest()
            ->limit($limit);

        if ($driver) {
            $query->whereRaw("json_extract(metadata, '$.driver') = ?", [$driver]);
        }

        return $query->get();
    }

    private function emptyStats(): array
    {
        return [
            'total' => 0,
            'verified' => 0,
            'failed' => 0,
            'errors' => 0,
            'success_rate' => 0,
        ];
    }
}
