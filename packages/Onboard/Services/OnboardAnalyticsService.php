<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboard Analytics Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Services;

use Onboard\Models\DocumentUpload;
use Onboard\Models\Onboarding;
use Onboard\Models\TaskCompletion;
use Onboard\Models\TrainingProgress;

class OnboardAnalyticsService
{
    /**
     * Get aggregate onboarding stats.
     */
    public function overview(): array
    {
        return [
            'total_onboarding' => Onboarding::count(),
            'completed' => Onboarding::where('status', 'completed')->count(),
            'in_progress' => Onboarding::where('status', 'in_progress')->count(),
            'overdue' => Onboarding::where('status', 'overdue')->count(),
        ];
    }

    /**
     * Get completion percentage for a specific user.
     */
    public function progress(int $userId): float
    {
        $onboarding = Onboarding::where('user_id', $userId)->first();
        if (!$onboarding) {
            return 0.0;
        }

        $template = $onboarding->template;
        $totalItems = $template->tasks()->count() + $template->documents()->count() + $template->training()->count();

        if ($totalItems === 0) {
            return 100.0;
        }

        $completedTasks = TaskCompletion::where('user_id', $userId)->count();
        $verifiedDocs = DocumentUpload::where('user_id', $userId)->where('status', 'verified')->count();
        $completedTraining = TrainingProgress::where('user_id', $userId)->where('status', 'completed')->count();

        return round((($completedTasks + $verifiedDocs + $completedTraining) / $totalItems) * 100, 2);
    }
}
