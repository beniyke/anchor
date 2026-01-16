<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Queue task for continuing a workflow execution.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Infrastructure;

use Queue\BaseTask;
use Queue\Scheduler;
use Workflow\Engine\WorkflowRunner;

class WorkflowContinuationTask extends BaseTask
{
    protected string $instanceId;

    public function __construct(string $instanceId)
    {
        $this->instanceId = $instanceId;
        parent::__construct(null);
    }

    public function period(Scheduler $scheduler): Scheduler
    {
        return $scheduler;
    }

    protected function execute(): bool
    {
        $runner = resolve(WorkflowRunner::class);
        $runner->execute($this->instanceId);

        return true;
    }

    protected function successMessage(): string
    {
        return "Workflow {$this->instanceId} continued successfully.";
    }

    protected function failedMessage(): string
    {
        return "Workflow {$this->instanceId} continuation failed.";
    }
}
