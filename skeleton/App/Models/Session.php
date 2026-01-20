<?php

declare(strict_types=1);

namespace App\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

class Session extends BaseModel
{
    protected string $table = 'session';

    protected array $fillable = ['user_id', 'token', 'browser', 'device', 'ip', 'os', 'expire_at'];

    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function findByToken(string $token): ?static
    {
        return static::query()->where('token', $token)->first();
    }

    public function scopeWhereToken(Builder $query, string $token): Builder
    {
        return $query->where('token', $token);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOlderThanCreated(Builder $query, DateTimeHelper $dateTime): Builder
    {
        return $query->olderThan('created_at', $dateTime);
    }

    public function isValid(int $inactivityTimeoutSeconds): bool
    {
        $now = DateTimeHelper::now();

        if (! empty($this->expire_at) && $this->expire_at->lessThan($now)) {
            return false;
        }

        $inactivityCutoff = $now->subSeconds($inactivityTimeoutSeconds);

        if ($this->updated_at->lessThan($inactivityCutoff)) {
            return false;
        }

        return true;
    }

    public function refresh(): bool
    {
        return $this->save();
    }

    public static function deleteByToken(string $token): int
    {
        return static::query()
            ->where('token', $token)
            ->delete();
    }

    public static function pruneExpired(): int
    {
        $now = DateTimeHelper::now();

        return static::query()
            ->whereLessThan('expire_at', $now)
            ->delete();
    }
}
