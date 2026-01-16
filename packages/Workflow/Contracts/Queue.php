<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for dispatching workflow tasks to the queue.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

use DateTimeImmutable;

interface Queue
{
    public function dispatchActivity(string $instanceId, string $activityClass, array $payload, ?ActivityOptions $options = null): void;

    public function dispatchContinuation(string $instanceId): void;

    public function dispatchTimer(string $instanceId, int $seconds): void;

    public function scheduleContinuationAt(DateTimeImmutable $time, string $instanceId): void;
}
