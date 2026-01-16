<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for registering Workflow components.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Providers;

use Core\Services\ServiceProvider;
use Workflow\Contracts\History;
use Workflow\Contracts\Queue;
use Workflow\Engine\WorkflowEngine;
use Workflow\Engine\WorkflowRunner;
use Workflow\Infrastructure\DatabaseHistoryRepository;
use Workflow\Infrastructure\QueueAdapter;

class WorkflowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(History::class, DatabaseHistoryRepository::class);
        $this->container->singleton(Queue::class, QueueAdapter::class);
        $this->container->singleton(WorkflowRunner::class);
        $this->container->singleton(WorkflowEngine::class);
    }

    public function boot(): void
    {
        require_once realpath(__DIR__ . '/../Helper/workflow.php');
    }
}
