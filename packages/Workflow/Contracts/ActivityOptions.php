<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Configuration options for workflow activities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Contracts;

class ActivityOptions
{
    public int $timeout = 60;

    public int $retries = 3;

    public int $retryDelay = 5; // seconds

    public string $queue = 'default';

    public static function make(): self
    {
        return new self();
    }

    public function withTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function withRetries(int $count): self
    {
        $this->retries = $count;

        return $this;
    }

    public function withRetryDelay(int $seconds): self
    {
        $this->retryDelay = $seconds;

        return $this;
    }

    public function onQueue(string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }
}
