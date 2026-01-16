<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Moderation Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services;

use App\Models\User;
use Audit\Audit;
use Pulse\Models\Post;
use Pulse\Models\Report;
use Pulse\Models\Thread;

class ModerationManagerService
{
    /**
     * Report a model (Thread or Post).
     */
    public function report(User $user, Thread|Post $model, string $reason): Report
    {
        return Report::create([
            'reportable_type' => get_class($model),
            'reportable_id' => $model->id,
            'user_id' => $user->id,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    public function pin(Thread $thread): void
    {
        $thread->update(['is_pinned' => true]);

        if (class_exists('Audit\Audit')) {
            Audit::log('pulse.thread.pinned', ['id' => $thread->id], $thread);
        }
    }

    public function lock(Thread $thread): void
    {
        $thread->update(['is_locked' => true]);

        if (class_exists('Audit\Audit')) {
            Audit::log('pulse.thread.locked', ['id' => $thread->id], $thread);
        }
    }

    /**
     * Resolve a report.
     */
    public function resolveReport(Report $report, string $status = 'resolved'): void
    {
        $report->update(['status' => $status]);
    }
}
