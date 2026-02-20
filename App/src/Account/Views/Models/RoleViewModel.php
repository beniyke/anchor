<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use Helpers\DateTimeHelper;
use Permit\Models\Role;

readonly class RoleViewModel
{
    private int $id;

    private string $name;

    private string $slug;

    private ?string $description;

    private array $permissions;

    private bool $hasUsers;

    private DateTimeHelper $createdAt;

    private DateTimeHelper $updatedAt;

    public function __construct(Role $role)
    {
        $this->id = $role->id;
        $this->name = $role->name;
        $this->slug = $role->slug;
        $this->description = $role->description;
        $this->permissions = $role->permissions()->get()->pluck('slug');
        $this->hasUsers = $role->users()->exists();
        $this->createdAt = $role->created_at;
        $this->updatedAt = $role->updated_at;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasUsers(): bool
    {
        return $this->hasUsers;
    }

    public function canBeDelete(): bool
    {
        return !$this->hasUsers();
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d');
    }

    public function hasPermission(string $key): bool
    {
        return in_array($key, $this->getPermissions());
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
            'title' => $this->getName(),
            'slug' => $this->getSlug(),
            'description' => $this->getDescription(),
            'permissions' => $this->getPermissions(),
            'has_users' => $this->hasUsers(),
            'can_be_delete' => $this->canBeDelete(),
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
