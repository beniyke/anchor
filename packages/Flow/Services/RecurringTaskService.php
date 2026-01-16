<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Recurring Task Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use Core\Services\ConfigServiceInterface;
use Database\DB;
use Flow\Models\Task;
use Flow\Services\Builders\RecurringBuilder;
use Helpers\DateTimeHelper;
use Throwable;

class RecurringTaskService
{
    public function __construct(
        protected ConfigServiceInterface $config,
        protected TaskService $taskService
    ) {
    }

    public function for(Task $task): RecurringBuilder
    {
        return new RecurringBuilder($task);
    }

    public function processRecurringTasks(): void
    {
        $tasks = Task::where('is_recurring', true)
            ->where('next_recurrence_at', '<=', DateTimeHelper::now()->toDateTimeString())
            ->get();

        foreach ($tasks as $task) {
            try {
                $this->createNextInstance($task);
            } catch (Throwable $e) {
                // Log error but continue with other tasks
                error_log("Failed to process recurring task [{$task->id}]: " . $e->getMessage());
            }
        }
    }

    public function createNextInstance(Task $task): ?Task
    {
        if (!$task->is_recurring || !$task->recurrence_pattern) {
            return null;
        }

        return DB::transaction(function () use ($task) {
            $newAttributes = $task->toArray();
            unset($newAttributes['id'], $newAttributes['created_at'], $newAttributes['updated_at']);

            $newAttributes['status'] = 'todo'; // Initial status for new instance
            $newAttributes['is_recurring'] = false; // The new one is an instance
            $newAttributes['next_recurrence_at'] = null;

            $nextInstance = new Task();
            $nextInstance->fill($newAttributes);
            $nextInstance->save();

            // Update the original task's next recurrence date
            $task->next_recurrence_at = $this->calculateNextDate(
                $task->next_recurrence_at ?? DateTimeHelper::now()->toDateTimeString(),
                $task->recurrence_pattern
            );
            $task->save();

            return $nextInstance;
        });
    }

    protected function calculateNextDate(string $currentDate, string $pattern): string
    {
        $date = DateTimeHelper::parse($currentDate);

        $nextDate = match ($pattern) {
            'daily' => $date->addDay(),
            'weekly' => $date->addWeek(),
            'monthly' => $date->addMonth(),
            default => $date->addDay(),
        };

        return $nextDate->toDateTimeString();
    }
}
