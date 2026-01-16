<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Subscription.
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
 * @property int             $pulse_thread_id
 * @property int             $user_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Thread $thread
 * @property-read User $user
 */
class Subscription extends BaseModel
{
    protected string $table = 'pulse_subscription';

    protected array $fillable = [
        'pulse_thread_id',
        'user_id',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'pulse_thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
