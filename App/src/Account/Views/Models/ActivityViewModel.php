<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * View model for presenting activity log data.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace App\Account\Views\Models;

use Activity\Models\Activity;
use App\Views\Models\UserViewModel;
use Helpers\DateTimeHelper;

readonly class ActivityViewModel
{
    private int $id;

    private string $description;

    private DateTimeHelper $createdAt;

    private ?int $userId;

    private ?UserViewModel $user;

    private ?array $metadata;

    private string $tag;

    private string $level;

    private ?int $subjectId;

    private ?string $subjectType;

    public function __construct(Activity $activity)
    {
        $this->id = $activity->id;
        $this->description = $activity->description;
        $this->createdAt = $activity->created_at;
        $this->userId = $activity->user_id;

        $user = $activity->user;
        $this->user = $user ? UserViewModel::basic($user) : null;
        $this->metadata = $activity->metadata;
        $this->tag = $activity->tag ?? 'general';
        $this->level = $activity->level ?? 'info';
        $this->subjectId = $activity->subject_id;
        $this->subjectType = $activity->subject_type;
    }

    public function getUser(): ?UserViewModel
    {
        return $this->user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function getTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->createdAt->format('Y-m-d H:i:s'));
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getSubjectId(): ?int
    {
        return $this->subjectId;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public static function collection(array $activities): array
    {
        return array_map(fn (Activity $entity) => new self($entity), $activities);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'user_id' => $this->getUserId(),
            'user' => $this->user?->toArray(),
            'tag' => $this->getTag(),
            'level' => $this->getLevel(),
            'metadata' => $this->getMetadata(),
            'subject_id' => $this->getSubjectId(),
            'subject_type' => $this->getSubjectType(),
            'created_at' => $this->getCreatedAt(),
            'time_ago' => $this->getTimeAgo(),
        ];
    }
}
