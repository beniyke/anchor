<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Reputation.
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
 * @property int             $user_id
 * @property int             $points
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 */
class Reputation extends BaseModel
{
    public const TABLE = 'pulse_reputation';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'user_id',
        'points',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
