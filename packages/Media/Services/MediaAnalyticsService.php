<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Media package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Services;

use Helpers\DateTimeHelper;
use Media\Models\Media;

class MediaAnalyticsService
{
    public function getStorageOverview(): array
    {
        return [
            'total_files' => Media::count(),
            'total_size_bytes' => Media::sum('size') ?? 0,
            'total_size_human' => $this->formatBytes(Media::sum('size') ?? 0),
            'by_type' => $this->getByType(),
            'by_collection' => $this->getByCollection(),
        ];
    }

    public function getByType(): array
    {
        return [
            'images' => Media::where('mime_type', 'LIKE', 'image/%')->count(),
            'videos' => Media::where('mime_type', 'LIKE', 'video/%')->count(),
            'audio' => Media::where('mime_type', 'LIKE', 'audio/%')->count(),
            'documents' => Media::where('mime_type', 'LIKE', 'application/%')->count(),
            'other' => Media::where('mime_type', 'NOT LIKE', 'image/%')
                ->where('mime_type', 'NOT LIKE', 'video/%')
                ->where('mime_type', 'NOT LIKE', 'audio/%')
                ->where('mime_type', 'NOT LIKE', 'application/%')
                ->count(),
        ];
    }

    public function getByCollection(): array
    {
        return Media::selectRaw('collection, COUNT(*) as file_count, SUM(size) as total_size')
            ->groupBy('collection')
            ->orderBy('file_count', 'desc')
            ->get()
            ->all();
    }

    public function getUploadTrends(int $days = 30): array
    {
        $trends = [];
        $startDate = DateTimeHelper::now()->subDays($days);

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i)->format('Y-m-d');
            $nextDate = $startDate->addDays($i + 1)->format('Y-m-d');

            $dayQuery = Media::where('created_at', '>=', $date)->where('created_at', '<', $nextDate);

            $trends[] = [
                'date' => $date,
                'uploads' => (clone $dayQuery)->count(),
                'size_bytes' => (clone $dayQuery)->sum('size') ?? 0,
            ];
        }

        return $trends;
    }

    public function getLargestFiles(int $limit = 10): array
    {
        return Media::orderBy('size', 'desc')
            ->limit($limit)
            ->get()
            ->all();
    }

    public function getMonthlyUsage(int $months = 12): array
    {
        $result = [];
        $startDate = DateTimeHelper::now()->subMonths($months);

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->addMonths($i)->startOfMonth()->format('Y-m-d');
            $monthEnd = $startDate->addMonths($i)->endOfMonth()->format('Y-m-d');

            $monthQuery = Media::where('created_at', '>=', $monthStart)->where('created_at', '<=', $monthEnd);

            $result[] = [
                'month' => $startDate->addMonths($i)->format('Y-m'),
                'uploads' => (clone $monthQuery)->count(),
                'size_bytes' => (clone $monthQuery)->sum('size') ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[(int) $factor]);
    }
}
