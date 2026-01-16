<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Goal Builder Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services\Builders;

use App\Models\User;
use DateTimeInterface;
use Metric\Models\Goal;
use Metric\Models\KeyResult;
use RuntimeException;

class GoalBuilderService
{
    protected array $data = [
        'status' => 'active',
        'type' => 'objective',
        'priority' => 'medium',
    ];

    protected array $keyResults = [];

    public function for(User $user): self
    {
        $this->data['user_id'] = $user->id;

        return $this;
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

    public function objective(): self
    {
        $this->data['type'] = 'objective';

        return $this;
    }

    public function personal(): self
    {
        $this->data['type'] = 'personal';

        return $this;
    }

    public function priority(string $priority): self
    {
        $this->data['priority'] = $priority;

        return $this;
    }

    public function dueAt(DateTimeInterface $date): self
    {
        $this->data['due_at'] = $date;

        return $this;
    }

    public function addKeyResult(string $title, float $target, string $unit = 'percentage'): self
    {
        $this->keyResults[] = [
            'title' => $title,
            'target_value' => $target,
            'unit' => $unit,
        ];

        return $this;
    }

    public function create(): Goal
    {
        if (empty($this->data['title']) || empty($this->data['user_id'])) {
            throw new RuntimeException("Goal requires a title and a user.");
        }

        $goal = Goal::create($this->data);

        foreach ($this->keyResults as $kr) {
            KeyResult::create(array_merge($kr, ['metric_goal_id' => $goal->id]));
        }

        return $goal;
    }
}
