<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for accessing and recording workflow history.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

interface History
{
    public function getHistory(string $instanceId): array;

    public function recordEvent(string $instanceId, string $eventType, array $payload = []): void;

    public function createNewInstance(string $workflowClass, string $businessKey, array $input): string;

    public function findActiveInstanceIdByBusinessKey(string $key): ?string;
}
