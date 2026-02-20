<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Training Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Services;

use App\Models\User;
use Audit\Audit;
use Helpers\DateTimeHelper;
use Onboard\Enums\TrainingStatus;
use Onboard\Models\Training;
use Onboard\Models\TrainingProgress;

class TrainingManagerService
{
    public function updateProgress(User $user, Training $training, string $status): TrainingProgress
    {
        $data = [
            'status' => $status,
        ];

        if ($status === TrainingStatus::COMPLETED->value || $status === TrainingStatus::COMPLETED) {
            $data['completed_at'] = DateTimeHelper::now();
        }

        $progress = TrainingProgress::updateOrCreate(
            ['user_id' => $user->id, 'onboard_training_id' => $training->id],
            $data
        );

        if (class_exists(Audit::class)) {
            Audit::log('onboard.training.updated', [
                'user' => $user->email,
                'training' => $training->name,
                'status' => $status
            ], $progress);
        }

        return $progress;
    }
}
