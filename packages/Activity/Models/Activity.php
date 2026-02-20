<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Activity model for storing log records.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Models;

use Activity\Models\Traits\HasDateKey;
use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;

class Activity extends BaseModel
{
    use HasDateKey;

    public const TABLE = 'activity';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'user_id',
        'subject_id',
        'subject_type',
        'description',
        'metadata',
        'session_id',
        'channel',
        'tag',
        'level',
        'date_key',
    ];

    protected array $casts = [
        'user_id' => 'integer',
        'subject_id' => 'integer',
        'description' => 'string',
        'metadata' => 'array',
        'tag' => 'string',
        'level' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject', 'subject_type', 'subject_id');
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

    public static function log(
        User | int $user,
        string $description,
        ?array $metadata = null,
        ?string $tag = null,
        string $level = 'info',
        ?BaseModel $subject = null,
        ?string $sessionId = null,
        string $channel = 'web'
    ): self {
        $userId = ($user instanceof User) ? $user->id : $user;

        $activity = new self([
            'user_id' => $userId,
            'subject_id' => $subject?->id,
            'subject_type' => $subject ? get_class($subject) : null,
            'description' => $description,
            'metadata' => $metadata,
            'session_id' => $sessionId,
            'channel' => $channel,
            'tag' => $tag,
            'level' => $level,
        ]);

        $activity->save();

        return $activity;
    }

    public static function prune(int $days = 30): int
    {
        $cutoffDate = DateTimeHelper::now()->subDays($days);

        return static::query()
            ->where('created_at', '<', $cutoffDate)
            ->delete();
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
