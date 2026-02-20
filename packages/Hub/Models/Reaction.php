<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reaction model for emoji reactions on messages.
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
 * @property string             $emoji
 * @property ?DateTimeInterface $created_at
 * @property-read Message $message
 */
class Reaction extends BaseModel
{
    public const TABLE = 'hub_reaction';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'message_id',
        'user_id',
        'emoji',
    ];

    protected array $casts = [
        'message_id' => 'int',
        'user_id' => 'int',
        'created_at' => 'datetime',
    ];

    public bool $timestamps = false;

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function scopeOnMessage(int $messageId): static
    {
        return $this->where('message_id', $messageId);
    }

    public function scopeWithEmoji(string $emoji): static
    {
        return $this->where('emoji', $emoji);
    }

    public function scopeByUser(int $userId): static
    {
        return $this->where('user_id', $userId);
    }
}
