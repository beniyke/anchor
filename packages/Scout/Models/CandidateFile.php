<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Candidate File.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $scout_candidate_id
 * @property string             $type
 * @property int                $media_id
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Candidate $candidate
 */
class CandidateFile extends BaseModel
{
    protected string $table = 'scout_candidate_file';

    protected array $fillable = [
        'scout_candidate_id',
        'type',
        'media_id',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'scout_candidate_id');
    }
}
