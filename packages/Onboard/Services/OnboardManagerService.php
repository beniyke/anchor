<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboard Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Services;

use App\Models\User;
use Audit\Audit;
use DateTimeInterface;
use Helpers\DateTimeHelper;
use Onboard\Models\DocumentUpload;
use Onboard\Models\Onboarding;
use Onboard\Models\Task;
use Onboard\Models\TaskCompletion;
use Onboard\Models\Template;
use RuntimeException;

class OnboardManagerService
{
    /**
     * Start onboarding for a user.
     */
    public function startOnboarding(User $user, Template $template, ?DateTimeInterface $dueAt = null): Onboarding
    {
        if (Onboarding::where('user_id', $user->id)->exists()) {
            throw new RuntimeException("Onboarding already exists for this user.");
        }

        $onboarding = Onboarding::create([
            'user_id' => $user->id,
            'onboard_template_id' => $template->id,
            'status' => 'in_progress',
            'started_at' => DateTimeHelper::now(),
            'due_at' => $dueAt,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('onboard.started', ['user' => $user->email, 'template' => $template->name], $onboarding);
        }

        return $onboarding;
    }

    /**
     * Mark a task as completed.
     */
    public function completeTask(User $user, Task $task, ?string $notes = null): TaskCompletion
    {
        $completion = TaskCompletion::updateOrCreate(
            ['user_id' => $user->id, 'onboard_task_id' => $task->id],
            ['completed_at' => DateTimeHelper::now(), 'notes' => $notes]
        );

        if (class_exists('Audit\Audit')) {
            Audit::log('onboard.task.completed', ['user' => $user->email, 'task' => $task->name], $completion);
        }

        $this->checkOnboardingCompletion($user);

        return $completion;
    }

    /**
     * Verify a document upload.
     */
    public function verifyDocument(DocumentUpload $upload, User $verifier): void
    {
        $upload->update([
            'status' => 'verified',
            'verified_at' => DateTimeHelper::now(),
            'verified_by' => $verifier->id,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('onboard.document.verified', ['id' => $upload->id], $upload);
        }

        $this->checkOnboardingCompletion(User::find($upload->user_id));
    }

    /**
     * Check if all required items are completed and update status.
     */
    protected function checkOnboardingCompletion(User $user): void
    {
        $onboarding = Onboarding::where('user_id', $user->id)->first();
        if (!$onboarding || $onboarding->status === 'completed') {
            return;
        }

        $template = $onboarding->template;

        // Check tasks
        $requiredTasks = $template->tasks()->where('is_required', true)->pluck('id');
        $completedTasks = TaskCompletion::where('user_id', $user->id)
            ->whereIn('onboard_task_id', $requiredTasks)
            ->count();

        if ($completedTasks < count($requiredTasks)) {
            return;
        }

        // Check documents
        $requiredDocs = $template->documents()->where('is_required', true)->pluck('id');
        $verifiedDocs = DocumentUpload::where('user_id', $user->id)
            ->whereIn('onboard_document_id', $requiredDocs)
            ->where('status', 'verified')
            ->count();

        if ($verifiedDocs < count($requiredDocs)) {
            return;
        }

        $onboarding->update([
            'status' => 'completed',
            'completed_at' => DateTimeHelper::now(),
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('onboard.completed', ['user' => $user->email], $onboarding);
        }

        // Potential handoff to Metric package
        $this->handoffToMetric($user);
    }

    /**
     * Handoff to Metric package.
     */
    protected function handoffToMetric(User $user): void
    {
        if (class_exists('Metric\Metric')) {
        }
    }
}
