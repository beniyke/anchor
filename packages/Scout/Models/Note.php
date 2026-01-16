<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Note.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\MorphTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $noteable_type
 * @property int                $noteable_id
 * @property int                $user_id
 * @property string             $content
 * @property bool               $is_private
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read BaseModel $noteable
 * @property-read User $author
 */
class Note extends BaseModel
{
    protected string $table = 'scout_note';

    protected array $fillable = [
        'noteable_type',
        'noteable_id',
        'user_id',
        'content',
        'is_private',
    ];

    protected array $casts = [
        'is_private' => 'boolean',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
