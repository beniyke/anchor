<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Goal.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;
use Metric\Enums\GoalStatus;

/**
 * @property int             $id
 * @property int             $user_id
 * @property string          $title
 * @property ?string         $description
 * @property string          $type
 * @property string          $priority
 * @property string          $status
 * @property float           $progress
 * @property ?DateTimeHelper $due_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read ModelCollection $keyResults
 */
class Goal extends BaseModel
{
    public const TABLE = 'metric_goal';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'progress',
        'due_at',
    ];

    protected array $casts = [
        'status' => GoalStatus::class,
        'progress' => 'float',
        'due_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(KeyResult::class, 'metric_goal_id');
    }
}
