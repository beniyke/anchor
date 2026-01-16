<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Metric Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services;

use App\Models\User;
use Audit\Audit;
use Helpers\DateTimeHelper;
use Metric\Models\Goal;
use Metric\Models\KeyResult;
use Metric\Models\Kpi;
use Metric\Models\KpiValue;

class MetricManagerService
{
    /**
     * Update progress of a key result and recalculate goal progress.
     */
    public function updateKeyResult(KeyResult $keyResult, float $value): void
    {
        $keyResult->update(['current_value' => $value]);

        $goal = $keyResult->goal;
        $totalProgress = 0;
        $keyResults = $goal->keyResults;

        foreach ($keyResults as $kr) {
            $progress = ($kr->current_value / $kr->target_value) * 100;
            $totalProgress += min(100, max(0, $progress));
        }

        $goal->update([
            'progress' => $totalProgress / $keyResults->count(),
            'status' => $goal->progress >= 100 ? 'completed' : 'active'
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('metric.goal.progress', [
                'goal' => $goal->title,
                'progress' => $goal->progress
            ], $goal);
        }
    }

    /**
     * Record a new KPI value.
     */
    public function recordKpi(Kpi $kpi, float $value, ?User $user = null, array $meta = []): KpiValue
    {
        $kpiValue = KpiValue::create([
            'metric_kpi_id' => $kpi->id,
            'user_id' => $user?->id,
            'value' => $value,
            'measured_at' => DateTimeHelper::now(),
            'meta' => $meta,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('metric.kpi.recorded', [
                'kpi' => $kpi->name,
                'value' => $value
            ], $kpiValue);
        }

        return $kpiValue;
    }

    public function findOrCreateKpi(string $name, string $unit = 'number'): Kpi
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        return Kpi::updateOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'unit' => $unit]
        );
    }
}
