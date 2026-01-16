<?php

declare(strict_types=1);

namespace App\Views\Models;

use App\Models\Activity;
use Helpers\DateTimeHelper;

readonly class ActivityViewModel
{
    private int $id;

    private string $description;

    private DateTimeHelper $createdAt;

    private ?int $userId;

    private ?UserViewModel $user;

    public function __construct(Activity $activity)
    {
        $this->id = $activity->id;
        $this->description = $activity->description;
        $this->createdAt = $activity->created_at;
        $this->userId = $activity->user_id;

        $user = $activity->user;
        $this->user = $user ? UserViewModel::basic($user) : null;
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
            'created_at' => $this->getCreatedAt(),
            'time_ago' => $this->getTimeAgo(),
        ];
    }
}
