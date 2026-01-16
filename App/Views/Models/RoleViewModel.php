<?php

declare(strict_types=1);

namespace App\Views\Models;

use App\Enums\AccessLevel;
use App\Enums\RoleType;
use App\Models\Role;
use Helpers\DateTimeHelper;

readonly class RoleViewModel
{
    private int $id;

    private string $title;

    private RoleType $type;

    private AccessLevel $access;

    private array $permission;

    private ?string $refid;

    private DateTimeHelper $createdAt;

    private DateTimeHelper $updatedAt;

    public function __construct(Role $role)
    {
        $this->id = $role->id;
        $this->title = $role->title;
        $this->type = $role->type;
        $this->access = $role->access;
        $this->permission = $role->permission ?? [];
        $this->refid = $role->refid;
        $this->createdAt = $role->created_at;
        $this->updatedAt = $role->updated_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): RoleType
    {
        return $this->type;
    }

    public function getAccessLevel(): AccessLevel
    {
        return $this->access;
    }

    public function getRefId(): string
    {
        return $this->refid;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d');
    }

    public function getFormattedType(): string
    {
        return ucfirst($this->type->name);
    }

    public function getFormattedAccess(): string
    {
        return ucfirst($this->access->name) . ' Access';
    }

    public function hasFullAccess(): bool
    {
        return $this->access === AccessLevel::Full;
    }

    public function hasPermission(string $key): bool
    {
        return isset($this->permission[$key]) && $this->permission[$key] === true;
    }

    public function getCreatedTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->createdAt->format('Y-m-d H:i:s'));
    }

    public function getUpdatedTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->updatedAt->format('Y-m-d H:i:s'));
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt->format('M j, Y');
    }

    public function getFormattedUpdatedAt(): string
    {
        return $this->updatedAt->format('M j, Y');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'type' => $this->getType()->value,
            'formatted_type' => $this->getFormattedType(),
            'access' => $this->getAccessLevel()->value,
            'formatted_access' => $this->getFormattedAccess(),
            'refid' => $this->getRefId(),
            'permission' => $this->permission,
            'has_full_access' => $this->hasFullAccess(),
            'created_at' => $this->getCreatedAt(),
            'formatted_created_at' => $this->getFormattedCreatedAt(),
            'created_time_ago' => $this->getCreatedTimeAgo(),
            'updated_at' => $this->getUpdatedAt(),
            'formatted_updated_at' => $this->getFormattedUpdatedAt(),
            'updated_time_ago' => $this->getUpdatedTimeAgo(),
        ];
    }

    public static function collection(array $roles): array
    {
        return array_map(fn (Role $model) => new self($model), $roles);
    }
}
