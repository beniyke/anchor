<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Application.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $scout_job_id
 * @property int                $scout_candidate_id
 * @property int                $scout_stage_id
 * @property string             $status
 * @property ?string            $rejection_reason
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Job $job
 * @property-read Candidate $candidate
 * @property-read Stage $stage
 * @property-read ModelCollection $interviews
 * @property-read ?Offer $offer
 * @property-read ModelCollection $notes
 */
class Application extends BaseModel
{
    protected string $table = 'scout_application';

    protected array $fillable = [
        'scout_job_id',
        'scout_candidate_id',
        'scout_stage_id',
        'status',
        'rejection_reason',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'scout_job_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'scout_candidate_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'scout_stage_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'scout_application_id');
    }

    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class, 'scout_application_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'noteable_id')->where('noteable_type', static::class);
    }
}
