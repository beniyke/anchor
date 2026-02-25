<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Builders;

use Academy\Models\AcademyProgram;
use Academy\Services\ProgramManagerService;

class ProgramBuilder
{
    protected array $attributes = [];

    public function __construct(protected ProgramManagerService $service)
    {
    }

    public function titled(string $title): self
    {
        $this->attributes['title'] = $title;
        $this->attributes['slug'] = strtolower(str_replace(' ', '-', $title));

        return $this;
    }

    public function described(string $description): self
    {
        $this->attributes['description'] = $description;

        return $this;
    }

    public function withContent(string $content): self
    {
        $this->attributes['content'] = $content;

        return $this;
    }

    /**
     * Assign multiple instructors.
     */
    public function withInstructors(array $ids): self
    {
        $this->attributes['instructor_ids'] = $ids;

        return $this;
    }

    public function withMetadata(string $key, mixed $value): self
    {
        $this->attributes['metadata'][$key] = $value;

        return $this;
    }

    /**
     * Restrict program to specific users (sets default learners).
     */
    public function restrictedTo(array $userIds): self
    {
        return $this->withMetadata('allowed_user_ids', $userIds);
    }

    public function create(): AcademyProgram
    {
        return $this->service->create($this->attributes);
    }
}
