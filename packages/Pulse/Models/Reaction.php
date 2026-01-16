<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Reaction.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $pulse_post_id
 * @property int             $user_id
 * @property string          $type
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Post $post
 * @property-read User $user
 */
class Reaction extends BaseModel
{
    protected string $table = 'pulse_reaction';

    protected array $fillable = [
        'pulse_post_id',
        'user_id',
        'type',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'pulse_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
