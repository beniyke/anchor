<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Project Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Builders;

use App\Models\User;
use Flow\Models\Project;
use Flow\Services\ProjectService;
use RuntimeException;

class ProjectBuilder
{
    protected array $data = [];

    protected ?User $owner = null;

    public function __construct(protected ProjectService $service)
    {
    }

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

    public function owner(User $user): self
    {
        $this->owner = $user;

        return $this;
    }

    public function save(): Project
    {
        if (!$this->owner) {
            throw new RuntimeException("Project owner is required.");
        }

        return $this->service->create($this->data, $this->owner);
    }
}
