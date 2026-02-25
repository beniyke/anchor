<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\SubmissionStatus;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Database\Traits\HasRefid;

class AcademySubmission extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_submission';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'sub_';

    protected array $fillable = [
        'refid',
        'assessment_id',
        'enrolment_id',
        'status',
        'submitted_at',
        'due_at',
        'extended_until',
        'attempt_number',
        'time_spent',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'assessment_id' => 'integer',
        'enrolment_id' => 'integer',
        'status' => SubmissionStatus::class,
        'submitted_at' => 'datetime',
        'due_at' => 'datetime',
        'extended_until' => 'datetime',
        'attempt_number' => 'integer',
        'time_spent' => 'integer',
        'metadata' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(AcademyAssessment::class, 'assessment_id');
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(AcademyEnrolment::class, 'enrolment_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AcademyAnswer::class, 'submission_id');
    }

    public function grade(): HasOne
    {
        return $this->hasOne(AcademyGrade::class, 'submission_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(AcademyFeedback::class, 'submission_id');
    }
}
