<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AnalyticsManagerService for the Client package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Services;

use Client\Models\Client;
use Database\DB;
use Helpers\File\Cache;
use UnitEnum;

class AnalyticsManagerService
{
    /**
     * The aggregation interval.
     */
    protected string $interval = 'daily';

    /**
     * The reseller/owner ID scope.
     */
    protected ?string $resellerId = null;

    public function daily(): self
    {
        $this->interval = 'daily';

        return $this;
    }

    public function monthly(): self
    {
        $this->interval = 'monthly';

        return $this;
    }

    public function yearly(): self
    {
        $this->interval = 'yearly';

        return $this;
    }

    /**
     * Scope analytics to a specific reseller/owner.
     */
    public function forReseller(int|string $id): self
    {
        $this->resellerId = (string) $id;

        return $this;
    }

    protected function getDateFormat(): string
    {
        return match ($this->interval) {
            'monthly' => '%Y-%m',
            'yearly' => '%Y',
            default => '%Y-%m-%d',
        };
    }

    /**
     * Reset fluent options to defaults.
     */
    protected function resetFluentOptions(): void
    {
        $this->interval = 'daily';
        $this->resellerId = null;
    }

    public function statusBreakdown(?string $start = null, ?string $end = null): array
    {
        $cacheKey = 'client_analytics_status_breakdown_' . ($start ?? 'all') . '_' . ($end ?? 'all') . '_' . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $query = Client::query();

            if ($this->resellerId) {
                $query->where('owner_id', $this->resellerId);
            }

            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $stats = $query->select('status')
                ->selectRaw('count(*) as count')
                ->groupBy('status')
                ->get();

            $breakdown = [];
            foreach ($stats as $row) {
                $status = $row->status instanceof UnitEnum ? $row->status->value : $row->status;
                $breakdown[$status] = $row->count;
            }

            $this->resetFluentOptions();

            return $breakdown;
        });
    }

    /**
     * Get new client signups over a date range.
     *
     * @param string $start
     * @param string $end
     *
     * @return int
     */
    public function growth(string $start, string $end): int
    {
        $cacheKey = "client_analytics_growth_{$start}_{$end}_" . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $query = Client::query();

            if ($this->resellerId) {
                $query->where('owner_id', $this->resellerId);
            }

            $count = $query->whereBetween('created_at', [$start, $end])->count();
            $this->resetFluentOptions();

            return $count;
        });
    }

    public function segmentation(?string $start = null, ?string $end = null): array
    {
        $cacheKey = 'client_analytics_segmentation_' . ($start ?? 'all') . '_' . ($end ?? 'all') . '_' . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $query = Client::query();

            if ($this->resellerId) {
                $query->where('owner_id', $this->resellerId);
            }

            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }

            $total = (clone $query)->count();
            if ($total === 0) {
                $this->resetFluentOptions();

                return ['standalone' => 0, 'resold' => 0];
            }

            $resold = (clone $query)->whereNotNull('owner_id')->count();
            $standalone = $total - $resold;

            $this->resetFluentOptions();

            return [
                'standalone' => [
                    'count' => $standalone,
                    'percentage' => round(($standalone / $total) * 100, 2)
                ],
                'resold' => [
                    'count' => $resold,
                    'percentage' => round(($resold / $total) * 100, 2)
                ]
            ];
        });
    }

    public function topOwners(int $limit = 5, ?string $start = null, ?string $end = null): array
    {
        $query = Client::query()
            ->select('owner_id')
            ->selectRaw('count(*) as client_count')
            ->whereNotNull('owner_id')
            ->groupBy('owner_id')
            ->orderBy('client_count', 'DESC')
            ->limit($limit);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->get()->toArray();
    }

    public function signupTrends(string $start, string $end, ?string $interval = null): array
    {
        $this->interval = $interval ?? $this->interval;
        $cacheKey = "client_analytics_signup_trends_{$start}_{$end}_{$this->interval}_" . ($this->resellerId ?? 'all');

        return Cache::create()->remember($cacheKey, 3600, function () use ($start, $end) {
            $format = $this->getDateFormat();

            $query = DB::table('client');

            if ($this->resellerId) {
                $query->where('owner_id', $this->resellerId);
            }

            $results = $query
                ->select(DB::raw("DATE_FORMAT(created_at, '{$format}') as date"))
                ->selectRaw('count(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            $trends = [];
            foreach ($results as $row) {
                $trends[(string) $row['date']] = (int) $row['count'];
            }

            $this->resetFluentOptions();

            return $trends;
        });
    }
}
