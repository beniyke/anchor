<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Scorecard.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $scout_interview_id
 * @property int                $user_id
 * @property ?array             $ratings
 * @property ?string            $comments
 * @property ?string            $recommendation
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Interview $interview
 * @property-read User $reviewer
 */
class Scorecard extends BaseModel
{
    protected string $table = 'scout_scorecard';

    protected array $fillable = [
        'scout_interview_id',
        'user_id',
        'ratings',
        'comments',
        'recommendation',
    ];

    protected array $casts = [
        'ratings' => 'json',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'scout_interview_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
