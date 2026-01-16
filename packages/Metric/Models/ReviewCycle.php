<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Review Cycle.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $name
 * @property ?DateTimeInterface $start_at
 * @property ?DateTimeInterface $end_at
 * @property string             $status
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ModelCollection $reviews
 */
class ReviewCycle extends BaseModel
{
    protected string $table = 'metric_review_cycle';

    protected array $fillable = [
        'name',
        'start_at',
        'end_at',
        'status',
    ];

    protected array $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'metric_review_cycle_id');
    }
}
