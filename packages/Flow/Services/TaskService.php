<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Database\DB;
use Flow\Exceptions\ProjectNotFoundException;
use Flow\Exceptions\TaskNotFoundException;
use Flow\Models\Column;
use Flow\Models\Project;
use Flow\Models\Tag;
use Flow\Models\Task;
use Flow\Notifications\TaskAssignedNotification;
use Flow\Services\Builders\TaskBuilder;
use Helpers\Data;
use Mail\Mail;

class TaskService
{
    public function __construct(
        protected ConfigServiceInterface $config
    ) {
    }

    public function make(): TaskBuilder
    {
        return new TaskBuilder($this);
    }

    public function create(array $data, User $creator): Task
    {
        return DB::transaction(function () use ($data, $creator) {
            $task = new Task();
            $task->fill($data);
            $task->creator_id = $creator->id;

            if (!isset($data['column_id']) && isset($data['project_id'])) {
                $project = Project::find($data['project_id']);
                if (!$project) {
                    throw new ProjectNotFoundException("Project [{$data['project_id']}] not found.");
                }

                $firstColumn = $project->columns()->orderBy('order')->first();
                if ($firstColumn) {
                    $task->column_id = $firstColumn->id;
                }
            }

            if (!isset($data['order'])) {
                $task->order = Task::where('column_id', $task->column_id)->max('order') + 1;
            }

            $task->save();

            return $task;
        });
    }

    public function update(Task $task, array $data): bool
    {
        $task->fill($data);

        return $task->save();
    }

    public function findByRefid(string $refid): Task
    {
        $task = Task::query()->where('refid', $refid)->first();
        if (!$task) {
            throw new TaskNotFoundException("Task with refid [{$refid}] not found.");
        }

        return $task;
    }

    public function move(Task $task, Column $targetColumn, int $newOrder): bool
    {
        return DB::transaction(function () use ($task, $targetColumn, $newOrder) {
            $oldColumnId = $task->column_id;
            $oldOrder = $task->order;

            // Re-order tasks in target column to make room
            Task::where('column_id', $targetColumn->id)
                ->where('order', '>=', $newOrder)
                ->increment('order');

            // Update the task itself
            $task->column_id = $targetColumn->id;
            $task->order = $newOrder;
            $task->save();

            // Re-order tasks in old column to fill gap (if moved to different column)
            if ($oldColumnId !== $targetColumn->id) {
                Task::where('column_id', $oldColumnId)
                    ->where('order', '>', $oldOrder)
                    ->decrement('order');
            }

            return true;
        });
    }

    public function addAssignee(Task $task, User $user): void
    {
        $task->assignees()->attach((int) $user->id);

        // Trigger notification
        Mail::send(new TaskAssignedNotification(Data::make([
            'email' => $user->email,
            'name' => $user->name,
            'task_title' => $task->title,
            'task_refid' => $task->refid,
            'project_name' => $task->project->name,
            'priority' => $task->priority ?? 'medium',
            'due_date' => $task->due_date
                ? $task->due_date->format('M d, Y') : 'N/A',
            'task_url' => url($this->config->get('flow.urls.task', 'flow/tasks') . '/' . $task->refid),
        ])));
    }

    public function removeAssignee(Task $task, User $user): void
    {
        $task->assignees()->detach($user->id);
    }

    public function addDependency(Task $task, Task $dependency): void
    {
        $task->dependencies()->attach($dependency->id);
    }

    public function removeDependency(Task $task, Task $dependency): void
    {
        $task->dependencies()->detach($dependency->id);
    }

    public function addTag(Task $task, string|int|Tag $tag): void
    {
        if (is_string($tag)) {
            $tag = Tag::where('name', $tag)->first() ?? Tag::create(['name' => $tag]);
        }

        $id = ($tag instanceof Tag) ? (int) $tag->id : (int) $tag;
        $task->tags()->attach($id);
    }

    public function removeTag(Task $task, string|int|Tag $tag): void
    {
        if (is_string($tag)) {
            $tag = Tag::where('name', $tag)->first();
        }

        if ($tag) {
            $id = ($tag instanceof Tag) ? $tag->id : $tag;
            $task->tags()->detach($id);
        }
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
