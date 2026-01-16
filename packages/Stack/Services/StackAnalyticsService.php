<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Analytics service for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Stack\Models\Form;

class StackAnalyticsService
{
    public function getSubmissionTrends(Form $form, int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('stack_submission')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('stack_form_id', $form->id)
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->all();
    }

    public function getConversionMetrics(Form $form): array
    {
        $views = DB::table('stack_event')
            ->where('stack_form_id', $form->id)
            ->where('event_type', 'view')
            ->count();

        $submissions = DB::table('stack_submission')
            ->where('stack_form_id', $form->id)
            ->count();

        return [
            'views' => $views,
            'submissions' => $submissions,
            'conversion_rate' => $views > 0 ? round(($submissions / $views) * 100, 2) : 0,
        ];
    }

    public function getErrorMectrics(Form $form, int $days = 30): int
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('stack_event')
            ->where('stack_form_id', $form->id)
            ->where('event_type', 'error')
            ->where('created_at', '>=', $since)
            ->count();
    }

    /**
     * Get daily events summary across all forms.
     */
    public function getGlobalTrends(int $days = 30): array
    {
        $since = DateTimeHelper::now()->subDays($days)->toDateTimeString();

        return DB::table('stack_event')
            ->selectRaw('event_type, COUNT(*) as event_count')
            ->where('created_at', '>=', $since)
            ->groupBy('event_type')
            ->get()
            ->all();
    }
}
