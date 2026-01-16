<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Flow Service Provider
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Providers;

use Core\Services\ServiceProvider;
use Flow\Models\Attachment;
use Flow\Models\Comment;
use Flow\Models\Project;
use Flow\Models\Task;
use Flow\Services\CollaborationService;
use Flow\Services\ProjectService;
use Flow\Services\RecurringTaskService;
use Flow\Services\ReportingService;
use Flow\Services\TaskService;
use Helpers\String\Str;

class FlowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(ProjectService::class);
        $this->container->singleton(TaskService::class);
        $this->container->singleton(CollaborationService::class);
        $this->container->singleton(RecurringTaskService::class);
        $this->container->singleton(ReportingService::class);
    }

    public function boot(): void
    {
        Project::creating(function ($project) {
            if (empty($project->refid)) {
                $project->refid = Str::random('secure');
            }
        });

        Task::creating(function ($task) {
            if (empty($task->refid)) {
                $task->refid = Str::random('secure');
            }
        });

        Attachment::creating(function ($attachment) {
            if (empty($attachment->refid)) {
                $attachment->refid = Str::random('secure');
            }
        });

        Comment::creating(function ($comment) {
            if (empty($comment->refid)) {
                $comment->refid = Str::random('secure');
            }
        });
    }
}
