<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Mention model for @mentions in messages.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $message_id
 * @property int                $user_id
 * @property ?DateTimeInterface $notified_at
 * @property-read Message $message
 */
class Mention extends BaseModel
{
    public const TABLE = 'hub_mention';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'message_id',
        'user_id',
        'notified_at',
    ];

    protected array $casts = [
        'message_id' => 'int',
        'user_id' => 'int',
        'notified_at' => 'datetime',
    ];

    public bool $timestamps = false;

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function isNotified(): bool
    {
        return $this->notified_at !== null;
    }

    public function scopeUnnotified(): static
    {
        return $this->whereNull('notified_at');
    }

    public function scopeForUser(int $userId): static
    {
        return $this->where('user_id', $userId);
    }
}
