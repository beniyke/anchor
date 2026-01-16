<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * LogBuilder provides a fluent interface for generating audit log entries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Services\Builders;

use App\Models\User;
use Audit\Models\AuditLog;
use Audit\Services\AuditManagerService;
use Database\BaseModel;

class LogBuilder
{
    protected string $event;

    protected array $data = [];

    protected ?BaseModel $model = null;

    protected ?User $user = null;

    public function __construct(
        private readonly AuditManagerService $manager
    ) {
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Set the target model (auditable).
     */
    public function on(BaseModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the user who performed the action.
     */
    public function by(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set old values (for manual change tracking).
     */
    public function old(array $values): self
    {
        $this->data['old_values'] = $values;

        return $this;
    }

    /**
     * Set new values (for manual change tracking).
     */
    public function new(array $values): self
    {
        $this->data['new_values'] = $values;

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->data['metadata'] = array_merge($this->data['metadata'] ?? [], $metadata);

        return $this;
    }

    /**
     * Add a single metadata item.
     */
    public function with(string $key, mixed $value): self
    {
        $this->data['metadata'][$key] = $value;

        return $this;
    }

    /**
     * Persist the audit log entry.
     */
    public function log(): AuditLog
    {
        return $this->manager->log(
            $this->event,
            $this->data,
            $this->model,
            $this->user
        );
    }
}
