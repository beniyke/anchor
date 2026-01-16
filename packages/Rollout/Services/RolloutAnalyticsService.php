<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for Rollout package (Feature Flags).
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Services;

use Helpers\DateTimeHelper;
use Rollout\Models\Feature;
use Rollout\Models\Segment;

class RolloutAnalyticsService
{
    public function getOverview(): array
    {
        $features = Feature::get();

        return [
            'total_features' => $features->count(),
            'enabled_features' => $features->filter(fn ($f) => $f->is_enabled)->count(),
            'disabled_features' => $features->filter(fn ($f) => !$f->is_enabled)->count(),
            'scheduled_features' => $features->filter(fn ($f) => $f->starts_at || $f->ends_at)->count(),
            'with_percentage' => $features->filter(fn ($f) => $f->percentage > 0 && $f->percentage < 100)->count(),
            'full_rollout' => $features->filter(fn ($f) => $f->percentage === 100)->count(),
        ];
    }

    public function getFeatureSummary(): array
    {
        $features = Feature::with('segments')->get();
        $result = [];

        foreach ($features as $feature) {
            $result[] = [
                'id' => $feature->id,
                'name' => $feature->name,
                'slug' => $feature->slug,
                'is_enabled' => $feature->is_enabled,
                'percentage' => $feature->percentage,
                'segment_count' => $feature->segments->count(),
                'is_active' => $feature->isActive(),
                'starts_at' => $feature->starts_at?->format('Y-m-d H:i'),
                'ends_at' => $feature->ends_at?->format('Y-m-d H:i'),
            ];
        }

        return $result;
    }

    public function getPercentageDistribution(): array
    {
        return [
            '0%' => Feature::where('percentage', 0)->count(),
            '1-25%' => Feature::where('percentage', '>', 0)->where('percentage', '<=', 25)->count(),
            '26-50%' => Feature::where('percentage', '>', 25)->where('percentage', '<=', 50)->count(),
            '51-75%' => Feature::where('percentage', '>', 50)->where('percentage', '<=', 75)->count(),
            '76-99%' => Feature::where('percentage', '>', 75)->where('percentage', '<', 100)->count(),
            '100%' => Feature::where('percentage', 100)->count(),
        ];
    }

    public function getByStatus(): array
    {
        $now = DateTimeHelper::now();
        $features = Feature::get();

        return [
            'active' => $features->filter(fn ($f) => $f->isActive())->count(),
            'inactive' => $features->filter(fn ($f) => !$f->is_enabled)->count(),
            'scheduled' => $features->filter(fn ($f) => $f->starts_at && $f->starts_at->isFuture())->count(),
            'expired' => $features->filter(fn ($f) => $f->ends_at && $f->ends_at->isPast())->count(),
        ];
    }

    public function getSegmentStats(): array
    {
        return [
            'total_segments' => Segment::count(),
            'by_type' => Segment::selectRaw('type, COUNT(*) as segment_count')
                ->groupBy('type')
                ->get()
                ->all(),
        ];
    }
}
