<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Import package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Services;

use Helpers\DateTimeHelper;
use Import\Enums\ImportStatus;
use Import\Models\ImportError;
use Import\Models\ImportHistory;

class ImportAnalyticsService
{
    /**
     * Get import statistics for a time period.
     */
    public function getStats(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ImportHistory::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalRows = (clone $query)->sum('total_rows') ?? 0;
        $successRows = (clone $query)->sum('success_rows') ?? 0;
        $failedRows = (clone $query)->sum('failed_rows') ?? 0;

        return [
            'total_imports' => (clone $query)->count(),
            'completed_imports' => (clone $query)->whereIn('status', [ImportStatus::COMPLETED, ImportStatus::PARTIAL])->count(),
            'failed_imports' => (clone $query)->where('status', ImportStatus::FAILED)->count(),
            'partial_imports' => (clone $query)->where('status', ImportStatus::PARTIAL)->count(),
            'total_rows_processed' => $totalRows,
            'success_rows' => $successRows,
            'failed_rows' => $failedRows,
            'row_success_rate' => $totalRows > 0 ? round(($successRows / $totalRows) * 100, 2) : 0,
            'avg_processing_time' => $this->calculateAvgProcessingTime($query),
        ];
    }

    public function getCommonErrors(int $limit = 10): array
    {
        return ImportError::selectRaw('error, COUNT(*) as error_count')
            ->groupBy('error')
            ->orderBy('error_count', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getDailyTrends(int $days = 30): array
    {
        $trends = [];
        $startDate = DateTimeHelper::now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i)->format('Y-m-d');
            $nextDate = $startDate->addDays($i + 1)->format('Y-m-d');

            $dayQuery = ImportHistory::where('created_at', '>=', $date)
                ->where('created_at', '<', $nextDate);

            $trends[] = [
                'date' => $date,
                'total' => (clone $dayQuery)->count(),
                'rows_imported' => (clone $dayQuery)->sum('success_rows') ?? 0,
            ];
        }

        return $trends;
    }

    /**
     * Get import health score (0-100).
     */
    public function getHealthScore(): int
    {
        $recentImports = ImportHistory::where('created_at', '>=', DateTimeHelper::now()->subDays(7))->get();

        if ($recentImports->isEmpty()) {
            return 100;
        }

        $totalScore = 0;
        $count = 0;

        foreach ($recentImports as $import) {
            if ($import->total_rows > 0) {
                $successRate = ($import->success_rows / $import->total_rows) * 100;
                $totalScore += $successRate;
                $count++;
            }
        }

        return $count > 0 ? (int) round($totalScore / $count) : 100;
    }

    private function calculateAvgProcessingTime($query): ?float
    {
        $imports = (clone $query)
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();

        if ($imports->isEmpty()) {
            return null;
        }

        $totalSeconds = 0;
        $count = 0;

        foreach ($imports as $import) {
            if ($import->started_at && $import->completed_at) {
                $totalSeconds += $import->completed_at->diffInSeconds($import->started_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalSeconds / $count, 2) : null;
    }
}
