<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reporting Proxy
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Proxies;

use Flow\Models\Project;
use Flow\Services\ReportingService;

class ReportingProxy
{
    public function __construct(
        protected ReportingService $service,
        protected Project $project
    ) {
    }

    public function completionRate(): float
    {
        return $this->service->getProjectCompletionRate($this->project);
    }

    public function burndown(): array
    {
        return $this->service->getBurndownData($this->project);
    }

    public function kanbanData(): array
    {
        return $this->service->getKanbanData($this->project);
    }

    public function userStats(): array
    {
        return $this->service->getUserTaskStats($this->project);
    }

    public function taskDistribution(string $groupBy = 'priority'): array
    {
        return $this->service->getTaskDistribution($this->project, $groupBy);
    }

    // Chart-ready helpers
    public function burndownChart(): array
    {
        return $this->service->getBurndownChartData($this->project);
    }

    public function distributionChart(string $groupBy = 'status'): array
    {
        return $this->service->getDistributionChartData($this->project, $groupBy);
    }

    public function userStatsChart(): array
    {
        return $this->service->getUserStatsChartData($this->project);
    }
}
