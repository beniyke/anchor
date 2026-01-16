<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Workflow
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow;

use Workflow\Contracts\History;
use Workflow\Engine\WorkflowRunner;

/**
 * Workflow Facade
 *
 * Provides a simple static interface for starting and executing workflows.
 */
class Workflow
{
    /**
     * Start a new workflow instance and trigger its first execution.
     *
     * @param string      $workflowClass The class name of the workflow
     * @param array       $input         Input data for the workflow
     * @param string|null $businessKey   Optional business key for searching
     *
     * @return string The generated instance ID
     */
    public static function run(string $workflowClass, array $input = [], ?string $businessKey = null): string
    {
        $history = resolve(History::class);
        $businessKey = $businessKey ?? uniqid('bk_', true);

        $instanceId = $history->createNewInstance($workflowClass, $businessKey, $input);

        self::execute($instanceId);

        return $instanceId;
    }

    /**
     * Execute or resume a workflow instance.
     *
     * @param string $instanceId The ID of the instance to execute
     */
    public static function execute(string $instanceId): void
    {
        $runner = resolve(WorkflowRunner::class);
        $runner->execute($instanceId);
    }
}
