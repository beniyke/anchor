<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification model for the dedicated Notification package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Notification\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;
use Helpers\File\Cache;
use Notification\Models\Traits\ClearsNotificationCache;

class Notification extends BaseModel
{
    use ClearsNotificationCache;

    public const TABLE = 'notification';

    protected string $table = self::TABLE;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', 0);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', 1);
    }

    public function scopeLatestForUser(Builder $query, int|string $user_id): Builder
    {
        return $query->where('user_id', $user_id)->latest();
    }

    public function markAsRead(): bool
    {
        $this->is_read = true;

        return $this->save();
    }

    public static function markAllAsRead(int|string $user_id): int
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

    public static function deleteAll(int|string $user_id): int
    {
        $result = self::query()
            ->where('user_id', $user_id)
            ->delete();

        if ($result > 0) {
            Cache::create('query')->flushTags(['notifications', "user:{$user_id}"]);
        }

        return $result;
    }

    public static function unreadForUser(int|string $userId, int $limit = 20): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->latest()
            ->limit($limit)
            ->cache(60)
            ->cacheTags(['notifications', "user:{$userId}", 'unread'])
            ->get()
            ->all();
    }

    public static function unreadCountForUser(int|string $userId): int
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->cache(60)
            ->cacheTags(['notifications', "user:{$userId}", 'unread'])
            ->count();
    }

    public static function readForUser(int|string $userId, int $limit = 20): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('is_read', 1)
            ->latest()
            ->limit($limit)
            ->cache(60)
            ->cacheTags(['notifications', "user:{$userId}", 'read'])
            ->get()
            ->all();
    }

    public static function recentForUser(int|string $userId, int $limit = 10): array
    {
        return static::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->cache(300)
            ->cacheTags(['notifications', "user:{$userId}", 'recent'])
            ->get()
            ->all();
    }

    public static function prune(int $days = 30, bool $pruneUnread = false): int
    {
        $cutoffDate = DateTimeHelper::now()->subDays($days);
        $query = static::query()->where('created_at', '<', $cutoffDate);

        if (! $pruneUnread) {
            $query->where('is_read', true);
        }

        return $query->delete();
    }
}
