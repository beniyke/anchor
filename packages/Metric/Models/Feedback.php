<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Feedback.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $metric_review_id
 * @property int                $user_id
 * @property int                $author_id
 * @property string             $content
 * @property string             $type
 * @property bool               $is_anonymous
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read User $user
 * @property-read User $author
 */
class Feedback extends BaseModel
{
    public const TABLE = 'metric_feedback';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'metric_review_id',
        'user_id',
        'author_id',
        'content',
        'type',
        'is_anonymous',
    ];

    protected array $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
