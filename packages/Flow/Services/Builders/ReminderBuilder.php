<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reminder Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Builders;

use App\Models\User;
use Flow\Models\Reminder;
use Flow\Models\Task;
use Flow\Services\ReminderService;
use Helpers\DateTimeHelper;
use Throwable;

class ReminderBuilder
{
    protected Task $task;

    protected User $user;

    protected int $value;

    protected string $unit = 'hours';

    protected string $type = 'before_due';

    protected ?string $remindAtOverride = null;

    public function __construct(
        protected ReminderService $service
    ) {
    }

    public function for(Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function notify(User $user): self
    {
        return $this->user($user);
    }

    public function user(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function at(string $value): self
    {
        $value = trim($value);

        // Pattern for duration: "2 hours", "1 day", etc.
        if (preg_match('/^\d+\s+(minute|hour|day|week|month)s?$/i', $value)) {
            $parts = explode(' ', $value);
            $this->value = (int) $parts[0];
            $this->unit = $parts[1];
            $this->type = 'before_due';
            $this->remindAtOverride = null;
        } else {
            // Try to parse as absolute date/time
            try {
                $date = DateTimeHelper::parse($value);
                $this->remindAtOverride = $date->toDateTimeString();
                $this->type = 'absolute';
            } catch (Throwable $e) {
                // Fallback to old behavior if parsing fails
                $parts = explode(' ', $value);
                $this->value = (int) ($parts[0] ?? 0);
                $this->unit = $parts[1] ?? 'hours';
                $this->type = 'before_due';
            }
        }

        return $this;
    }

    public function beforeDue(): self
    {
        $this->type = 'before_due';

        return $this;
    }

    public function save(): Reminder
    {
        $remindAt = $this->remindAtOverride ?? null;

        if ($this->type === 'before_due' && $this->task->due_date) {
            $remindAt = $this->task->due_date->copy()->sub($this->value, $this->unit)->toDateTimeString();
        }

        return $this->service->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'type' => $this->type,
            'value' => $this->value ?? 0,
            'unit' => $this->unit ?? 'hours',
            'remind_at' => $remindAt,
            'status' => 'active'
        ]);
    }
}
