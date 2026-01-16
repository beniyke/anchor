<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Application Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services\Builders;

use RuntimeException;
use Scout\Models\Application;
use Scout\Models\Candidate;
use Scout\Models\Job;
use Scout\Services\ScoutManagerService;

class ApplicationBuilder
{
    protected ScoutManagerService $manager;

    protected ?Job $job = null;

    protected ?Candidate $candidate = null;

    public function __construct(ScoutManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function for(Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function from(Candidate $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function submit(): Application
    {
        if (!$this->job || !$this->candidate) {
            throw new RuntimeException("Application requires a job and a candidate.");
        }

        return $this->manager->apply($this->candidate, $this->job);
    }
}
