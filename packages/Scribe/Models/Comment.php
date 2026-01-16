<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Comment.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $scribe_post_id
 * @property int             $user_id
 * @property string          $content
 * @property string          $status
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Post $post
 */
class Comment extends BaseModel
{
    protected string $table = 'scribe_comment';

    protected array $fillable = [
        'refid',
        'scribe_post_id',
        'user_id',
        'content',
        'status',
    ];

    protected array $casts = [
        'scribe_post_id' => 'int',
        'user_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'scribe_post_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
