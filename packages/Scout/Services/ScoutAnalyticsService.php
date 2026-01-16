<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Scout Analytics Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services;

use Database\DB;
use Scout\Models\Application;
use Scout\Models\Candidate;
use Scout\Models\Job;

class ScoutAnalyticsService
{
    public function funnel(Job $job): array
    {
        return Application::where('scout_job_id', $job->id)
            ->join('scout_stage', 'scout_application.scout_stage_id', '=', 'scout_stage.id')
            ->select('scout_stage.name', DB::raw('count(*) as count'))
            ->groupBy('scout_stage.name', 'scout_stage.order')
            ->orderBy('scout_stage.order')
            ->get()
            ->toArray();
    }

    /**
     * Get aggregate recruitment stats.
     */
    public function overview(): array
    {
        return [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::where('status', 'published')->count(),
            'total_applicants' => Candidate::count(),
            'total_applications' => Application::count(),
            'hired_count' => Application::where('status', 'hired')->count(),
        ];
    }

    /**
     * Get time-to-hire average (in days).
     */
    public function averageTimeToHire(): float
    {
        // Simple diff between creation and hire status update
        return Application::where('status', 'hired')
            ->select(DB::raw('AVG(DATEDIFF(updated_at, created_at)) as avg_days'))
            ->value('avg_days') ?? 0.0;
    }
}
