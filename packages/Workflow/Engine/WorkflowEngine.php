<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core engine for managing workflow execution and state.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Engine;

use Exception;
use Helpers\File\Contracts\LoggerInterface;
use Throwable;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;

class WorkflowEngine
{
    private History $history;

    private Queue $queue;

    private LoggerInterface $logger;

    public function __construct(History $history, Queue $queue, LoggerInterface $logger)
    {
        $this->history = $history;
        $this->queue = $queue;
        $this->logger = $logger->setLogFile('workflow.log');
    }

    public function activityCompleted(string $instanceId, array $result): void
    {
        $this->history->recordEvent($instanceId, 'ActivityCompleted', ['result' => $result]);
        $this->queue->dispatchContinuation($instanceId);
        $this->logger->info("Activity completed for $instanceId. Workflow resuming.");
    }

    public function activityFailed(string $instanceId, string $activityClass, array $payload, string $error): void
    {
        $this->logger->error("Activity $activityClass failed for $instanceId: $error");

        $this->history->recordEvent($instanceId, 'ActivityFailed', [
            'activity' => $activityClass,
            'error' => $error,
            'original_payload' => $payload,
        ]);

        $activity = resolve($activityClass);
        try {
            $activity->onFailure($instanceId, new Exception($error));
            $this->logger->info("$activityClass onFailure handler executed.");
        } catch (Throwable $e) {
            $this->logger->critical("COMPENSATION FAILURE: onFailure for $activityClass failed: " . $e->getMessage());
        }

        $this->queue->dispatchContinuation($instanceId);
    }

    public function timerFired(string $instanceId): void
    {
        $this->history->recordEvent($instanceId, 'TimerFired');
        $this->queue->dispatchContinuation($instanceId);
        $this->logger->info("Timer fired for $instanceId. Workflow resuming.");
    }

    public function compensate(string $instanceId, string $activityClass, array $originalPayload): void
    {
        $this->logger->warning("Running compensation for $activityClass on $instanceId.");
        $this->history->recordEvent($instanceId, 'CompensationStarted', ['activity' => $activityClass]);

        $activity = resolve($activityClass);

        try {
            $activity->compensate($instanceId, $originalPayload);
            $this->history->recordEvent($instanceId, 'CompensationCompleted', ['activity' => $activityClass]);
            $this->logger->info("Compensation for $activityClass successful.");
        } catch (Throwable $e) {
            $this->history->recordEvent($instanceId, 'CompensationFailed', ['activity' => $activityClass, 'error' => $e->getMessage()]);
            $this->logger->critical("Compensation for $activityClass FAILED: " . $e->getMessage());
        }
    }
}
