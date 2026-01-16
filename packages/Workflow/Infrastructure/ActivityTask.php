<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Queue task for executing a workflow activity.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Infrastructure;

use Queue\BaseTask;
use Queue\Scheduler;
use Throwable;
use Workflow\Engine\WorkflowEngine;

class ActivityTask extends BaseTask
{
    protected string $instanceId;

    protected string $activityClass;

    protected array $activityPayload;

    public function __construct(string $instanceId, string $activityClass, array $payload)
    {
        $this->instanceId = $instanceId;
        $this->activityClass = $activityClass;
        $this->activityPayload = $payload;
        parent::__construct(null);
    }

    public function period(Scheduler $scheduler): Scheduler
    {
        return $scheduler;
    }

    protected function execute(): bool
    {
        $activity = resolve($this->activityClass);
        $engine = resolve(WorkflowEngine::class);

        try {
            $result = $activity->handle($this->activityPayload);
            $engine->activityCompleted($this->instanceId, $result);

            return true;
        } catch (Throwable $e) {
            $engine->activityFailed($this->instanceId, $this->activityClass, $this->activityPayload, $e->getMessage());

            return false;
        }
    }

    protected function successMessage(): string
    {
        return "Activity {$this->activityClass} completed for workflow {$this->instanceId}.";
    }

    protected function failedMessage(): string
    {
        return "Activity {$this->activityClass} failed for workflow {$this->instanceId}.";
    }
}
