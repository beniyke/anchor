<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Project Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Database\DB;
use Flow\Exceptions\ProjectNotFoundException;
use Flow\Models\Column;
use Flow\Models\Project;
use Flow\Services\Builders\ProjectBuilder;

class ProjectService
{
    public function __construct(
        protected ConfigServiceInterface $config
    ) {
    }

    public function make(): ProjectBuilder
    {
        return new ProjectBuilder($this);
    }

    public function create(array $data, User $owner): Project
    {
        return DB::transaction(function () use ($data, $owner) {
            $project = new Project();
            $project->fill($data);
            $project->owner_id = $owner->id;
            $project->save();

            // Create default columns
            $this->createDefaultColumns($project);

            return $project;
        });
    }

    public function update(Project $project, array $data): bool
    {
        $project->fill($data);

        return $project->save();
    }

    public function findByRefid(string $refid): Project
    {
        $project = Project::query()->where('refid', $refid)->first();
        if (!$project) {
            throw new ProjectNotFoundException("Project with refid [{$refid}] not found.");
        }

        return $project;
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function createColumn(Project $project, array $data): Column
    {
        $column = new Column();
        $column->project_id = $project->id;
        $column->fill($data);

        if (!isset($data['order'])) {
            $column->order = $project->columns()->count();
        }

        $column->save();

        return $column;
    }

    public function updateColumnOrder(Project $project, array $columnOrders): void
    {
        DB::transaction(function () use ($project, $columnOrders) {
            foreach ($columnOrders as $columnId => $order) {
                $column = Column::find($columnId);
                if ($column && $column->project_id === $project->id) {
                    $column->order = $order;
                    $column->save();
                }
            }
        });
    }

    protected function createDefaultColumns(Project $project): void
    {
        $defaults = $this->config->get('flow.default_columns', [
            ['name' => 'To Do', 'type' => 'todo', 'order' => 0],
            ['name' => 'In Progress', 'type' => 'doing', 'order' => 1],
            ['name' => 'Done', 'type' => 'done', 'order' => 2],
        ]);

        foreach ($defaults as $data) {
            $this->createColumn($project, $data);
        }
    }
}
