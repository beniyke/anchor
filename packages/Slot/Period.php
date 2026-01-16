<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Represents a time period with a start and end time.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot;

use Helpers\DateTimeHelper;
use InvalidArgumentException;

class Period
{
    public function __construct(
        public readonly DateTimeHelper $start,
        public readonly DateTimeHelper $end,
        public readonly array $options = []
    ) {
        if ($this->start >= $this->end) {
            throw new InvalidArgumentException('Period start must be before end');
        }
    }

    public static function make(DateTimeHelper|string $start, DateTimeHelper|string $end): self
    {
        $start = is_string($start) ? DateTimeHelper::parse($start) : $start;
        $end = is_string($end) ? DateTimeHelper::parse($end) : $end;

        return new self($start, $end);
    }

    public function options(array $options): self
    {
        return new self($this->start, $this->end, array_merge($this->options, $options));
    }

    public function daily(int $interval = 1, DateTimeHelper|string|null $endsAt = null): self
    {
        return $this->options([
            'recurrence_rule' => ['frequency' => 'daily', 'interval' => $interval],
            'recurrence_ends_at' => $endsAt ? (is_string($endsAt) ? DateTimeHelper::parse($endsAt) : $endsAt) : null,
        ]);
    }

    public function weekly(int $interval = 1, DateTimeHelper|string|null $endsAt = null): self
    {
        return $this->options([
            'recurrence_rule' => ['frequency' => 'weekly', 'interval' => $interval],
            'recurrence_ends_at' => $endsAt ? (is_string($endsAt) ? DateTimeHelper::parse($endsAt) : $endsAt) : null,
        ]);
    }

    public function monthly(int $interval = 1, DateTimeHelper|string|null $endsAt = null): self
    {
        return $this->options([
            'recurrence_rule' => ['frequency' => 'monthly', 'interval' => $interval],
            'recurrence_ends_at' => $endsAt ? (is_string($endsAt) ? DateTimeHelper::parse($endsAt) : $endsAt) : null,
        ]);
    }

    public function yearly(int $interval = 1, DateTimeHelper|string|null $endsAt = null): self
    {
        return $this->options([
            'recurrence_rule' => ['frequency' => 'yearly', 'interval' => $interval],
            'recurrence_ends_at' => $endsAt ? (is_string($endsAt) ? DateTimeHelper::parse($endsAt) : $endsAt) : null,
        ]);
    }

    public function title(string $title): self
    {
        return $this->options(['title' => $title]);
    }

    public function metadata(array $metadata): self
    {
        return $this->options(['metadata' => $metadata]);
    }

    public function status(string|object $status): self
    {
        $value = is_object($status) && enum_exists(get_class($status)) ? $status->value : $status;

        return $this->options(['status' => $value]);
    }

    public function pending(): self
    {
        return $this->status('pending');
    }

    public function confirmed(): self
    {
        return $this->status('confirmed');
    }

    public function cancelled(): self
    {
        return $this->status('cancelled');
    }

    public function type(string|object $type): self
    {
        $value = is_object($type) && enum_exists(get_class($type)) ? $type->value : $type;

        return $this->options(['type' => $value]);
    }

    public function asAvailability(): self
    {
        return $this->type('availability');
    }

    public function asAppointment(): self
    {
        return $this->type('appointment');
    }

    public function asBlocked(): self
    {
        return $this->type('blocked');
    }

    public function asCustom(): self
    {
        return $this->type('custom');
    }

    public function gap(int $minutes): self
    {
        return $this->options(['gap' => $minutes]);
    }

    public function buffer(int $before = 0, int $after = 0): self
    {
        return $this->options([
            'buffer_before' => $before,
            'buffer_after' => $after,
        ]);
    }

    public function slotDuration(int $minutes): self
    {
        return $this->options(['slot_duration' => $minutes]);
    }

    public function allowOverlap(string|object ...$types): self
    {
        $rules = $this->options['overlap_rules'] ?? [];
        foreach ($types as $type) {
            $value = is_object($type) && enum_exists(get_class($type)) ? $type->value : $type;
            $rules[$value] = true;
        }

        return $this->options(['overlap_rules' => $rules]);
    }

    public function denyOverlap(string|object ...$types): self
    {
        $rules = $this->options['overlap_rules'] ?? [];
        foreach ($types as $type) {
            $value = is_object($type) && enum_exists(get_class($type)) ? $type->value : $type;
            $rules[$value] = false;
        }

        return $this->options(['overlap_rules' => $rules]);
    }

    public function allowAvailability(): self
    {
        return $this->allowOverlap('availability');
    }

    public function allowAppointment(): self
    {
        return $this->allowOverlap('appointment');
    }

    public function allowBlocked(): self
    {
        return $this->allowOverlap('blocked');
    }

    public function allowCustom(): self
    {
        return $this->allowOverlap('custom');
    }

    public function denyAvailability(): self
    {
        return $this->denyOverlap('availability');
    }

    public function denyAppointment(): self
    {
        return $this->denyOverlap('appointment');
    }

    public function denyBlocked(): self
    {
        return $this->denyOverlap('blocked');
    }

    public function denyCustom(): self
    {
        return $this->denyOverlap('custom');
    }

    public static function fromDuration(DateTimeHelper $start, int $minutes): self
    {
        return new self($start, (clone $start)->addMinutes($minutes));
    }

    public function overlaps(Period $other): bool
    {
        return $this->start < $other->end && $this->end > $other->start;
    }

    public function contains(DateTimeHelper $datetime): bool
    {
        return $datetime >= $this->start && $datetime < $this->end;
    }

    public function duration(): int
    {
        return (int) $this->start->diffInMinutes($this->end);
    }

    public function split(int $minutes, int $gap = 0): array
    {
        if ($minutes <= 0) {
            throw new InvalidArgumentException('Slot duration must be positive');
        }

        $slots = [];
        $current = clone $this->start;

        while ($current < $this->end) {
            $slotEnd = (clone $current)->addMinutes($minutes);

            if ($slotEnd > $this->end) {
                break;
            }

            $slots[] = new self($current, $slotEnd);

            $current = (clone $slotEnd)->addMinutes($gap);
        }

        return $slots;
    }

    public function addBuffer(int $beforeMinutes, int $afterMinutes): self
    {
        $newStart = (clone $this->start)->subMinutes($beforeMinutes);
        $newEnd = (clone $this->end)->addMinutes($afterMinutes);

        return new self($newStart, $newEnd);
    }

    public function intersection(Period $other): ?self
    {
        if (! $this->overlaps($other)) {
            return null;
        }

        $start = $this->start > $other->start ? $this->start : $other->start;
        $end = $this->end < $other->end ? $this->end : $other->end;

        return new self(clone $start, clone $end);
    }

    public function encompasses(Period $other): bool
    {
        return $this->start <= $other->start && $this->end >= $other->end;
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start->format('Y-m-d H:i:s'),
            'end' => $this->end->format('Y-m-d H:i:s'),
            'duration' => $this->duration(),
        ];
    }

    public function toString(): string
    {
        return sprintf(
            '%s to %s (%d minutes)',
            $this->start->format('Y-m-d H:i'),
            $this->end->format('Y-m-d H:i'),
            $this->duration()
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
