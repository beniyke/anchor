<?php

declare(strict_types=1);

namespace App\Views\Models;

use App\Models\Notification;
use Helpers\DateTimeHelper;

readonly class NotificationViewModel
{
    private int $id;

    private string $message;

    private ?string $url;

    private string $label;

    private bool $isRead;

    private DateTimeHelper $createdAt;

    public function __construct(Notification $notification)
    {
        $this->id = $notification->id;
        $this->message = $notification->message;
        $this->url = $notification->url;
        $this->label = $notification->label;
        $this->isRead = $notification->is_read;
        $this->createdAt = $notification->created_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->createdAt->format('Y-m-d H:i:s'));
    }

    public static function collection(array $notifications): array
    {
        return array_map(fn (Notification $model) => new self($model), $notifications);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'url' => $this->getUrl(),
            'label' => $this->getLabel(),
            'is_read' => $this->isRead(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'time_ago' => $this->getTimeAgo(),
        ];
    }
}
