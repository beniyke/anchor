<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command wrapper for executing workflow activities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Commands;

use ReflectionClass;
use Workflow\Contracts\Activity;
use Workflow\Contracts\ActivityOptions;
use Workflow\Contracts\Command;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;

class ActivityCommand implements Command
{
    private Activity $activity;

    private ?ActivityOptions $options;

    public function __construct(Activity $activity, ?ActivityOptions $options = null)
    {
        $this->activity = $activity;
        $this->options = $options;
    }

    public function getName(): string
    {
        return (new ReflectionClass($this->activity))->getShortName();
    }

    public function execute(string $instanceId, History $history, Queue $queue): void
    {
        $payload = [];
        if (method_exists($this->activity, 'getPayload')) {
            $payload = $this->activity->getPayload();
        } elseif (property_exists($this->activity, 'payload')) {
            $ref = new ReflectionClass($this->activity);
            $prop = $ref->getProperty('payload');
            $prop->setAccessible(true);
            $payload = $prop->getValue($this->activity);
        }

        $queue->dispatchActivity($instanceId, get_class($this->activity), $payload, $this->options);
    }

    public function replay(array $recordedEvent): void
    {
    }

    public function getPayload(): array
    {
        return [];
    }
}
