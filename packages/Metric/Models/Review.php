<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Review.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $metric_review_cycle_id
 * @property int                $user_id
 * @property int                $reviewer_id
 * @property float              $rating
 * @property ?string            $summary
 * @property string             $status
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ReviewCycle $cycle
 * @property-read User $user
 * @property-read User $reviewer
 * @property-read ModelCollection $feedback
 */
class Review extends BaseModel
{
    protected string $table = 'metric_review';

    protected array $fillable = [
        'metric_review_cycle_id',
        'user_id',
        'reviewer_id',
        'rating',
        'summary',
        'status',
    ];

    protected array $casts = [
        'rating' => 'float',
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(ReviewCycle::class, 'metric_review_cycle_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'metric_review_id');
    }
}
