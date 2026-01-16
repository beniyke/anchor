<?php

declare(strict_types=1);

namespace App\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

class Activity extends BaseModel
{
    protected string $table = 'activity';

    protected array $fillable = [
        'user_id',
        'description',
    ];

    protected array $casts = [
        'user_id' => 'integer',
        'description' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function setDescriptionAttribute(string $value): void
    {
        $this->attributes['description'] = trim($value);
    }

    public function getDescriptionForDisplay(): string
    {
        return ucfirst($this->description);
    }

    public function getActorName(): string
    {
        return $this->user->name;
    }

    public function formatSummary(string $timeAgo): string
    {
        return sprintf(
            '%s %s (%s)',
            $this->getActorName(),
            $this->getDescriptionForDisplay(),
            $timeAgo
        );
    }

    public function scopeLatestForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId)->latest();
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        $cutoffDate = DateTimeHelper::now()->subDays($days);

        return $query->since('created_at', $cutoffDate);
    }

    public function scopeToday(Builder $query): Builder
    {
        $startOfDay = DateTimeHelper::now()->startOfDay();
        $endOfDay = DateTimeHelper::now()->endOfDay();

        return $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
    }

    public static function log(User | int $user, string $description): self
    {
        $userId = ($user instanceof User) ? $user->id : $user;

        $activity = new self([
            'user_id' => $userId,
            'description' => $description,
        ]);

        $activity->save();

        return $activity;
    }

    public static function recentActivities(int $limit = 50): array
    {
        return static::query()
            ->with('user')
            ->latest()
            ->limit($limit)
            ->cache(600) // 10 minutes
            ->cacheTags(['activities', 'recent'])
            ->get()
            ->all();
    }

    public static function forUser(int $userId, int $limit = 20): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->cache(900) // 15 minutes
            ->cacheTags(['activities', "user:{$userId}"])
            ->get()
            ->all();
    }

    public static function todayActivities(int $limit = 100): array
    {
        return static::query()
            ->with('user')
            ->today()
            ->latest()
            ->limit($limit)
            ->cache(300) // 5 minutes
            ->cacheTags(['activities', 'today'])
            ->get()
            ->all();
    }
}
