<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Recurring Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Builders;

use Flow\Models\Task;
use Helpers\DateTimeHelper;
use RuntimeException;

class RecurringBuilder
{
    protected array $data = [
        'is_recurring' => true,
    ];

    public function __construct(protected Task $task)
    {
    }

    public function daily(): self
    {
        $this->data['recurrence_pattern'] = 'daily';

        return $this;
    }

    public function weekly(): self
    {
        $this->data['recurrence_pattern'] = 'weekly';

        return $this;
    }

    public function monthly(): self
    {
        $this->data['recurrence_pattern'] = 'monthly';

        return $this;
    }

    public function startingAt(string $dateTime): self
    {
        $this->data['next_recurrence_at'] = DateTimeHelper::parse($dateTime)->toDateTimeString();

        return $this;
    }

    public function save(): bool
    {
        if (!isset($this->data['recurrence_pattern'])) {
            throw new RuntimeException("Recurrence pattern is required.");
        }

        return $this->task->update($this->data);
    }
}
