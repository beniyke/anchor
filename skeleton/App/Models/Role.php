<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccessLevel;
use App\Enums\RoleType;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\HasMany;

class Role extends BaseModel
{
    protected string $table = 'role';

    protected array $fillable = ['title', 'type', 'access', 'permission', 'refid'];

    protected array $casts = [
        'title' => 'string',
        'refid' => 'string',
        'type' => RoleType::class,
        'access' => AccessLevel::class,
        'permission' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    public function scopeFullAccess(Builder $query): Builder
    {
        return $query->where('access', AccessLevel::Full->value);
    }

    public function scopeReadOnly(Builder $query): Builder
    {
        return $query->where('access', AccessLevel::Read->value);
    }

    public function scopeOfType(Builder $query, RoleType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeWhereRefid(Builder $query, string $refid): Builder
    {
        return $query->where('refid', $refid);
    }

    public function scopeSearchTitle(Builder $query, string $searchQuery): Builder
    {
        $search = '%'.strtolower(trim($searchQuery)).'%';

        return $query->whereLike('title', $search);
    }

    public function hasFullAccess(): bool
    {
        return $this->access === AccessLevel::Full;
    }

    public function hasPermission(string $key): bool
    {
        $permissions = $this->permission ?? [];

        return isset($permissions[$key]) && $permissions[$key] === true;
    }

    public static function allCached(): array
    {
        return static::query()
            ->cache(86400) // 24 hours
            ->cacheTags(['roles', 'system'])
            ->get()
            ->all();
    }

    public static function findByRefid(string $refid): ?static
    {
        return static::query()
            ->where('refid', $refid)
            ->cache(86400) // 24 hours
            ->cacheTags(['roles', "role:refid:{$refid}"])
            ->first();
    }

    public static function fullAccessRoles(): array
    {
        return static::query()
            ->fullAccess()
            ->cache(86400) // 24 hours
            ->cacheTags(['roles', 'full-access'])
            ->get()
            ->all();
    }
}
