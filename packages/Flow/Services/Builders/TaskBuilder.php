<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Builders;

use App\Models\User;
use Flow\Models\Task;
use Flow\Services\TaskService;
use RuntimeException;

class TaskBuilder
{
    protected array $data = [];

    protected ?User $creator = null;

    public function __construct(protected TaskService $service)
    {
    }

    public function title(string $title): self
    {
        $this->data['title'] = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function project(int|string $projectId): self
    {
        $this->data['project_id'] = $projectId;

        return $this;
    }

    public function column(int|string $columnId): self
    {
        $this->data['column_id'] = $columnId;

        return $this;
    }

    public function priority(string $priority): self
    {
        $this->data['priority'] = $priority;

        return $this;
    }

    public function parent(int|string $parentId): self
    {
        $this->data['parent_id'] = $parentId;

        return $this;
    }

    public function creator(User $user): self
    {
        $this->creator = $user;

        return $this;
    }

    public function save(): Task
    {
        if (!$this->creator) {
            throw new RuntimeException("Task creator is required.");
        }

        return $this->service->create($this->data, $this->creator);
    }
}
