<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Key Result.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $metric_goal_id
 * @property string             $title
 * @property float              $target_value
 * @property float              $current_value
 * @property string             $unit
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Goal $goal
 */
class KeyResult extends BaseModel
{
    protected string $table = 'metric_key_result';

    protected array $fillable = [
        'metric_goal_id',
        'title',
        'target_value',
        'current_value',
        'unit',
    ];

    protected array $casts = [
        'target_value' => 'float',
        'current_value' => 'float',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'metric_goal_id');
    }
}
