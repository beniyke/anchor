<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Interface for defining a workflow orchestration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

use Generator;

interface Workflow
{
    public function execute(array $input): Generator;

    public function handleSignal(string $signalName, array $payload): void;
}
