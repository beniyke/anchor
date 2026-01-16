<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent reminder builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services\Builders;

use Database\BaseModel;
use Helpers\DateTimeHelper;
use Hub\Enums\RepeatInterval;
use Hub\Models\Reminder;
use Hub\Services\HubManagerService;

class ReminderBuilder
{
    private int $userId;

    private ?string $remindableType = null;

    private ?int $remindableId = null;

    private string $message = '';

    private ?DateTimeHelper $remindAt = null;

    private RepeatInterval $repeatInterval = RepeatInterval::NONE;

    private array $metadata = [];

    public function __construct(
        private readonly HubManagerService $manager
    ) {
    }

    /**
     * Set the user to remind.
     */
    public function for(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Attach reminder to a resource.
     */
    public function about(BaseModel $model): self
    {
        $this->remindableType = get_class($model);
        $this->remindableId = $model->id;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set reminder time.
     */
    public function at(DateTimeHelper $datetime): self
    {
        $this->remindAt = $datetime;

        return $this;
    }

    /**
     * Set reminder in minutes from now.
     */
    public function inMinutes(int $minutes): self
    {
        $this->remindAt = DateTimeHelper::now()->addMinutes($minutes);

        return $this;
    }

    /**
     * Set reminder in hours from now.
     */
    public function inHours(int $hours): self
    {
        $this->remindAt = DateTimeHelper::now()->addHours($hours);

        return $this;
    }

    /**
     * Set reminder in days from now.
     */
    public function inDays(int $days): self
    {
        $this->remindAt = DateTimeHelper::now()->addDays($days);

        return $this;
    }

    /**
     * Set tomorrow at specific time.
     */
    public function tomorrow(int $hour = 9, int $minute = 0): self
    {
        $this->remindAt = DateTimeHelper::now()
            ->addDay()
            ->setHour($hour)
            ->setMinute($minute)
            ->setSecond(0);

        return $this;
    }

    public function repeat(RepeatInterval|string $interval): self
    {
        if (is_string($interval)) {
            $interval = RepeatInterval::from($interval);
        }

        $this->repeatInterval = $interval;

        return $this;
    }

    public function daily(): self
    {
        $this->repeatInterval = RepeatInterval::DAILY;

        return $this;
    }

    public function weekly(): self
    {
        $this->repeatInterval = RepeatInterval::WEEKLY;

        return $this;
    }

    public function monthly(): self
    {
        $this->repeatInterval = RepeatInterval::MONTHLY;

        return $this;
    }

    public function metadata(array $data): self
    {
        $this->metadata = $data;

        return $this;
    }

    /**
     * Add metadata key.
     */
    public function with(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    public function create(): Reminder
    {
        return $this->manager->createReminder([
            'user_id' => $this->userId,
            'remindable_type' => $this->remindableType,
            'remindable_id' => $this->remindableId,
            'message' => $this->message,
            'remind_at' => $this->remindAt,
            'repeat_interval' => $this->repeatInterval,
            'metadata' => $this->metadata,
        ]);
    }
}
