<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command wrapper for executing inline closures as activities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Commands;

use Closure;
use LogicException;
use Workflow\Contracts\Command;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;

class InlineActivity implements Command
{
    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function getName(): string
    {
        return 'InlineActivity';
    }

    public function execute(string $instanceId, History $history, Queue $queue): void
    {
        throw new LogicException('InlineActivity should be executed synchronously by the WorkflowRunner.');
    }

    public function replay(array $recordedEvent): void
    {
    }

    public function getPayload(): array
    {
        return [];
    }

    public function getClosure(): Closure
    {
        return $this->closure;
    }
}
