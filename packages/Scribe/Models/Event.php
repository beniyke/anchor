<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $scribe_post_id
 * @property string          $event_type
 * @property ?int            $user_id
 * @property ?string         $session_id
 * @property ?array          $data
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Post $post
 */
class Event extends BaseModel
{
    protected string $table = 'scribe_event';

    protected array $fillable = [
        'scribe_post_id',
        'event_type',
        'user_id',
        'session_id',
        'data',
    ];

    protected array $casts = [
        'scribe_post_id' => 'int',
        'user_id' => 'int',
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'scribe_post_id');
    }
}
