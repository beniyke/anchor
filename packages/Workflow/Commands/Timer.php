<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command wrapper for scheduling workflow timers.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Commands;

use Workflow\Contracts\Command;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;

class Timer implements Command
{
    private int $seconds;

    public function __construct(int $seconds)
    {
        $this->seconds = $seconds;
    }

    public function getName(): string
    {
        return 'Timer';
    }

    public function execute(string $instanceId, History $history, Queue $queue): void
    {
        $queue->dispatchTimer($instanceId, $this->seconds);
    }

    public function replay(array $recordedEvent): void
    {
    }

    public function getPayload(): array
    {
        return ['seconds' => $this->seconds];
    }
}
