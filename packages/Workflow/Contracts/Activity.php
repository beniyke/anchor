<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for defining a workflow activity.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

use Throwable;

interface Activity
{
    public function handle(array $payload): array;

    public function onFailure(string $instanceId, Throwable $e): void;

    public function compensate(string $instanceId, array $originalPayload): void;
}
