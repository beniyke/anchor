<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Task Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Services\Builders;

use Onboard\Models\Task;
use Onboard\Models\Template;
use RuntimeException;

class TaskBuilder
{
    protected array $data = [
        'order' => 0,
        'is_required' => true,
    ];

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function template(Template $template): self
    {
        $this->data['onboard_template_id'] = $template->id;

        return $this;
    }

    public function order(int $order): self
    {
        $this->data['order'] = $order;

        return $this;
    }

    public function optional(): self
    {
        $this->data['is_required'] = false;

        return $this;
    }

    public function create(): Task
    {
        if (empty($this->data['name']) || empty($this->data['onboard_template_id'])) {
            throw new RuntimeException("Task requires a name and a template.");
        }

        return Task::create($this->data);
    }
}
