<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Runner for executing workflow instances and handling commands.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Engine;

use Closure;
use Helpers\File\Contracts\LoggerInterface;
use ReflectionClass;
use Throwable;
use Workflow\Commands\ActivityCommand;
use Workflow\Commands\InlineActivity;
use Workflow\Commands\SideEffect;
use Workflow\Contracts\Activity;
use Workflow\Contracts\Command;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;

class WorkflowRunner
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

    public function execute(string $instanceId): void
    {
        $history = $this->history->getHistory($instanceId);

        if (empty($history)) {
            $this->logger->error("History not found for $instanceId.");

            return;
        }

        $workflowClass = $history[0]['workflow_class'];
        $input = $history[0]['input'];
        $this->logger->info("Executing workflow: $workflowClass");

        if (! class_exists($workflowClass)) {
            $this->logger->error("Workflow class $workflowClass not found.");

            return;
        }

        $workflow = new $workflowClass();
        $generator = $workflow->execute($input);

        $eventCounter = 1;

        while ($generator->valid()) {
            $current = $generator->current();

            if ($current instanceof Closure) {
                $command = new InlineActivity($current);
            } elseif ($current instanceof Activity) {
                $options = null;
                if (property_exists($current, 'options')) {
                    $ref = new ReflectionClass($current);
                    if ($ref->hasProperty('options')) {
                        $prop = $ref->getProperty('options');
                        $prop->setAccessible(true);
                        $options = $prop->getValue($current);
                    }
                }
                $command = new ActivityCommand($current, $options);
            } elseif ($current instanceof Command) {
                $command = $current;
            } else {
                $type = is_object($current) ? get_class($current) : gettype($current);
                $interfaces = is_object($current) ? implode(',', class_implements($current)) : 'N/A';
                $this->logger->error("Unknown yield type in workflow $instanceId: $type | Interfaces: $interfaces");

                return;
            }

            if (isset($history[$eventCounter])) {
                $recordedEvent = $history[$eventCounter];
                $commandResult = $recordedEvent['result'] ?? null;

                if ($recordedEvent['type'] === 'SignalReceived') {
                    $workflow->handleSignal($recordedEvent['payload']['name'], $recordedEvent['payload']['payload']);
                }

                $command->replay($recordedEvent);
                $generator->send($commandResult);
                $this->logger->debug("REPLAYED #$eventCounter: " . $recordedEvent['type']);
            } else {
                $this->logger->info("EXECUTE #$eventCounter: " . $command->getName());

                if ($command instanceof SideEffect || $command instanceof InlineActivity) {
                    try {
                        $closure = $command->getClosure();
                        $result = $closure();

                        $this->history->recordEvent($instanceId, $command->getName() . 'Completed', ['result' => $result]);
                        $generator->send($result);

                        $eventCounter++;
                        $generator->next();

                        continue;
                    } catch (Throwable $e) {
                        $this->logger->error('Synchronous command failed: ' . $e->getMessage());
                        throw $e;
                    }
                }

                $command->execute($instanceId, $this->history, $this->queue);

                return;
            }

            $eventCounter++;
            $generator->next();
        }

        $this->history->recordEvent($instanceId, 'WorkflowCompleted');
        $this->logger->info("Workflow $instanceId completed.");
    }
}
