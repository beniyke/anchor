<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Training Progress.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $user_id
 * @property int             $onboard_training_id
 * @property string          $status
 * @property ?DateTimeHelper $completed_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read Training $training
 */
class TrainingProgress extends BaseModel
{
    protected string $table = 'onboard_training_progress';

    protected array $fillable = [
        'user_id',
        'onboard_training_id',
        'status',
        'completed_at',
    ];

    protected array $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class, 'onboard_training_id');
    }
}
