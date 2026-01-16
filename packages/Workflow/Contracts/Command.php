<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for internal workflow commands.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

interface Command
{
    public function getName(): string;

    public function execute(string $instanceId, History $history, Queue $queue): void;

    public function replay(array $recordedEvent): void;

    public function getPayload(): array;
}
