<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Job Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services\Builders;

use DateTimeInterface;
use RuntimeException;
use Scout\Models\Job;
use Scout\Services\ScoutManagerService;

class JobBuilder
{
    protected ScoutManagerService $manager;

    protected array $data = [
        'status' => 'draft',
        'type' => 'full-time',
    ];

    public function __construct(ScoutManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function title(string $title): self
    {
        $this->data['title'] = $title;
        $this->data['slug'] = $this->manager->generateSlug($title, Job::class);

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function requirements(string $requirements): self
    {
        $this->data['requirements'] = $requirements;

        return $this;
    }

    public function location(string $location): self
    {
        $this->data['location'] = $location;

        return $this;
    }

    public function type(string $type): self
    {
        $this->data['type'] = $type;

        return $this;
    }

    public function salary(string $range): self
    {
        $this->data['salary_range'] = $range;

        return $this;
    }

    public function publish(): self
    {
        $this->data['status'] = 'published';

        return $this;
    }

    public function expiresAt(DateTimeInterface $date): self
    {
        $this->data['expires_at'] = $date;

        return $this;
    }

    public function create(): Job
    {
        if (empty($this->data['title']) || empty($this->data['description'])) {
            throw new RuntimeException("Job requires a title and description.");
        }

        return $this->manager->createJob($this->data);
    }
}
