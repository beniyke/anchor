<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Adapter for integrating Workflow with the system Queue.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Infrastructure;

use DateTimeImmutable;
use Queue\QueueManager;
use Workflow\Contracts\ActivityOptions;
use Workflow\Contracts\Queue;

class QueueAdapter implements Queue
{
    private QueueManager $manager;

    private string $queueIdentifier;

    public function __construct(QueueManager $manager, string $queueIdentifier = 'workflow')
    {
        $this->manager = $manager;
        $this->queueIdentifier = $queueIdentifier;
    }

    public function dispatchActivity(string $instanceId, string $activityClass, array $payload, ?ActivityOptions $options = null): void
    {
        $taskData = [
            'instanceId' => $instanceId,
            'activityClass' => $activityClass,
            'payload' => $payload,
        ];

        $identifier = $options?->queue ?? $this->queueIdentifier;

        $this->manager
            ->identifier($identifier)
            ->job(ActivityTask::class, $taskData)
            ->queue();
    }

    public function dispatchContinuation(string $instanceId): void
    {
        $this->manager
            ->identifier($this->queueIdentifier)
            ->job(WorkflowContinuationTask::class, ['instanceId' => $instanceId])
            ->queue();
    }

    public function dispatchTimer(string $instanceId, int $seconds): void
    {
        $this->manager
            ->identifier($this->queueIdentifier)
            ->job(WorkflowContinuationTask::class, [
                'instanceId' => $instanceId,
                'delay' => $seconds,
            ])
            ->queue();
    }

    public function scheduleContinuationAt(DateTimeImmutable $time, string $instanceId): void
    {
        $now = new DateTimeImmutable();
        $delay = $time->getTimestamp() - $now->getTimestamp();

        if ($delay > 0) {
            $this->manager
                ->identifier($this->queueIdentifier)
                ->job(WorkflowContinuationTask::class, [
                    'instanceId' => $instanceId,
                    'delay' => $delay,
                ])
                ->queue();
        }
    }
}
