<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Export package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Services;

use Export\Enums\ExportFormat;
use Export\Enums\ExportStatus;
use Export\Models\ExportHistory;
use Helpers\DateTimeHelper;

class ExportAnalyticsService
{
    /**
     * Get export statistics for a time period.
     */
    public function getStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ExportHistory::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_exports' => (clone $query)->count(),
            'completed_exports' => (clone $query)->where('status', ExportStatus::COMPLETED)->count(),
            'failed_exports' => (clone $query)->where('status', ExportStatus::FAILED)->count(),
            'pending_exports' => (clone $query)->where('status', ExportStatus::PENDING)->count(),
            'total_rows_exported' => (clone $query)->where('status', ExportStatus::COMPLETED)->sum('rows_count') ?? 0,
            'total_file_size' => (clone $query)->where('status', ExportStatus::COMPLETED)->sum('file_size') ?? 0,
            'success_rate' => $this->calculateSuccessRate($query),
            'avg_processing_time' => $this->calculateAvgProcessingTime($query),
        ];
    }

    public function getByFormat(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ExportHistory::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $result = [];

        foreach (ExportFormat::cases() as $format) {
            $formatQuery = clone $query;
            $result[$format->value] = $formatQuery->where('format', $format->value)->count();
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
                'total' => ExportHistory::where('created_at', '>=', $date)
                    ->where('created_at', '<', $nextDate)
                    ->count(),
                'completed' => ExportHistory::where('created_at', '>=', $date)
                    ->where('created_at', '<', $nextDate)
                    ->where('status', ExportStatus::COMPLETED)
                    ->count(),
            ];
        }

        return $trends;
    }

    public function getTopExporters(int $limit = 10): array
    {
        return ExportHistory::selectRaw('user_id, COUNT(*) as export_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('export_count', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    private function calculateSuccessRate($query): float
    {
        $total = (clone $query)->count();

        if ($total === 0) {
            return 0.0;
        }

        $completed = (clone $query)->where('status', ExportStatus::COMPLETED)->count();

        return round(($completed / $total) * 100, 2);
    }

    private function calculateAvgProcessingTime($query): ?float
    {
        $exports = (clone $query)
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->where('status', ExportStatus::COMPLETED)
            ->get();

        if ($exports->isEmpty()) {
            return null;
        }

        $totalSeconds = 0;
        $count = 0;

        foreach ($exports as $export) {
            if ($export->started_at && $export->completed_at) {
                $totalSeconds += $export->completed_at->diffInSeconds($export->started_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalSeconds / $count, 2) : null;
    }
}
