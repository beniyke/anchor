<?php

declare(strict_types=1);

/**
 * ActivityLogger is a service class for logging user activities with dynamic message formatting.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Services;

use App\Models\Activity;
use App\Services\Auth\Interfaces\AuthServiceInterface;

class ActivityLoggerService
{
    private ?string $description = null;

    private ?array $data = null;

    private ?int $user_id = null;

    private readonly AuthServiceInterface $auth;

    public function __construct(AuthServiceInterface $auth)
    {
        $this->auth = $auth;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function data(?array $data = null): self
    {
        $this->data = $data;

        return $this;
    }

    public function user(?int $user_id = null): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function log(): bool
    {
        $user_id = $this->user_id ?? $this->auth?->user()?->id;

        if (! $user_id) {
            throw new RuntimeException('No user ID specified or authenticated.');
        }

        $description = $this->interpolate($this->description, $this->data);

        Activity::log($user_id, $description);

        return true;
    }

    protected function interpolate(string $description, ?array $data = null): string
    {
        if (empty($data)) {
            return $description;
        }

        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($data) {
            $key = $matches[1];

            return $data[$key] ?? $matches[0];
        }, $description);
    }
}
