<?php

declare(strict_types=1);

namespace App\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\File\Cache;

class Notification extends BaseModel
{
    protected string $table = 'notification';

    protected array $fillable = ['user_id', 'message', 'url', 'label', 'is_read'];

    protected array $casts = [
        'user_id' => 'integer',
        'message' => 'string',
        'url' => 'string',
        'label' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        $clearCache = function ($notification) {
            Cache::create('query')->flushTags(['notifications', "user:{$notification->user_id}"]);
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeLatestForUser(Builder $query, int $user_id): Builder
    {
        return $query->where('user_id', $user_id)->latest();
    }

    public function markAsRead(): bool
    {
        $this->is_read = true;

        return $this->save();
    }

    public static function markAllAsRead(int $user_id): int
    {
        $result = self::query()
            ->where('user_id', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($result > 0) {
            Cache::create('query')->flushTags(['notifications', "user:{$user_id}"]);
        }

        return $result;
    }

    public static function deleteAll(int $user_id): int
    {
        $result = self::query()
            ->where('user_id', $user_id)
            ->delete();

        if ($result > 0) {
            Cache::create('query')->flushTags(['notifications', "user:{$user_id}"]);
        }

        return $result;
    }

    public static function unreadForUser(int $userId, int $limit = 20): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->latest()
            ->limit($limit)
            ->cache(60) // 1 minute
            ->cacheTags(['notifications', "user:{$userId}", 'unread'])
            ->get()
            ->all();
    }

    public static function unreadCountForUser(int $userId): int
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->cache(60) // 1 minute
            ->cacheTags(['notifications', "user:{$userId}", 'unread'])
            ->count();
    }

    public static function recentForUser(int $userId, int $limit = 10): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->cache(300) // 5 minutes
            ->cacheTags(['notifications', "user:{$userId}", 'recent'])
            ->get()
            ->all();
    }
}
