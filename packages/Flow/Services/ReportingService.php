<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reporting Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use Core\Services\ConfigServiceInterface;
use Flow\Enums\ColumnType;
use Flow\Models\Column;
use Flow\Models\Project;
use Flow\Services\Proxies\ReportingProxy;
use Helpers\DateTimeHelper;

class ReportingService
{
    public function __construct(
        protected ConfigServiceInterface $config
    ) {
    }

    public function for(Project $project): ReportingProxy
    {
        return new ReportingProxy($this, $project);
    }

    public function getProjectCompletionRate(Project $project): float
    {
        $total = $project->tasks()->count();
        if ($total === 0) {
            return 0.0;
        }

        $doneColumnIds = Column::where('type', ColumnType::DONE->value)->get()->pluck('id');

        $completed = $project->tasks()->whereIn('column_id', $doneColumnIds)->count();

        return ($completed / $total) * 100;
    }

    public function getBurndownData(Project $project): array
    {
        $days = 30;
        $data = [];

        $doneColumnIds = Column::where('type', ColumnType::DONE->value)->pluck('id');

        for ($i = $days; $i >= 0; $i--) {
            $day = DateTimeHelper::now()->subDays($i);
            $dateKey = $day->format('Y-m-d');
            $endOfDay = $day->endOfDay();

            $totalCreated = $project->tasks()->where('created_at', '<=', $endOfDay)->count();

            $doneCount = $project->tasks()
                ->whereIn('column_id', $doneColumnIds)
                ->where('updated_at', '<=', $endOfDay)
                ->count();

            $data[$dateKey] = max(0, $totalCreated - $doneCount);
        }

        return $data;
    }

    public function getKanbanData(Project $project): array
    {
        $columns = $project->columns()
            ->with(['tasks' => function ($query) {
                $query->with(['assignees', 'tags', 'comments', 'attachments'])
                    ->orderBy('order');
            }])
            ->get();

        $board = [];

        foreach ($columns as $column) {
            $board[] = [
                'id' => $column->id,
                'name' => $column->name,
                'type' => $column->type,
                'tasks' => $column->tasks->toArray()
            ];
        }

        return $board;
    }

    public function getUserTaskStats(Project $project): array
    {
        $stats = [];
        $tasks = $project->tasks()->with(['assignees', 'column'])->get();
        $users = [];
        foreach ($tasks as $task) {
            foreach ($task->assignees as $user) {
                if (!isset($users[$user->id])) {
                    $users[$user->id] = $user;
                    $stats[$user->id] = [
                        'user' => $user->toArray(),
                        'total' => 0,
                        'completed' => 0,
                        'overdue' => 0
                    ];
                }

                $stats[$user->id]['total']++;

                if ($task->column && $task->column->type === ColumnType::DONE->value) {
                    $stats[$user->id]['completed']++;
                }

                if ($task->due_date && $task->due_date < DateTimeHelper::now()) {
                    if (!$task->column || $task->column->type !== ColumnType::DONE->value) {
                        $stats[$user->id]['overdue']++;
                    }
                }
            }
        }

        return array_values($stats);
    }

    public function getTaskDistribution(Project $project, string $groupBy): array
    {
        // groupBy: 'priority', 'type', 'status' (column type)
        $tasks = $project->tasks()->with('column')->get();
        $distribution = [];

        foreach ($tasks as $task) {
            $key = 'unknown';

            if ($groupBy === 'status' && $task->column) {
                $key = $task->column->name;
            } else {
                $key = $task->{$groupBy} ?? 'none';
            }

            if (!isset($distribution[$key])) {
                $distribution[$key] = 0;
            }
            $distribution[$key]++;
        }

        return $distribution;
    }

    /**
     * Returns burndown data formatted for charting libraries.
     */
    public function getBurndownChartData(Project $project): array
    {
        $burndown = $this->getBurndownData($project);

        return [
            'labels' => array_keys($burndown),
            'datasets' => [
                [
                    'label' => 'Remaining Tasks',
                    'data' => array_values($burndown)
                ]
            ]
        ];
    }

    public function getDistributionChartData(Project $project, string $groupBy = 'status'): array
    {
        $distribution = $this->getTaskDistribution($project, $groupBy);

        return [
            'labels' => array_keys($distribution),
            'datasets' => [
                [
                    'label' => 'Tasks by ' . ucfirst($groupBy),
                    'data' => array_values($distribution)
                ]
            ]
        ];
    }

    public function getUserStatsChartData(Project $project): array
    {
        $stats = $this->getUserTaskStats($project);

        $labels = [];
        $totalData = [];
        $completedData = [];
        $overdueData = [];

        foreach ($stats as $userStat) {
            $labels[] = $userStat['user']['name'] ?? 'Unknown';
            $totalData[] = $userStat['total'];
            $completedData[] = $userStat['completed'];
            $overdueData[] = $userStat['overdue'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Total', 'data' => $totalData],
                ['label' => 'Completed', 'data' => $completedData],
                ['label' => 'Overdue', 'data' => $overdueData]
            ]
        ];
    }
}
