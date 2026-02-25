<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\AssessmentType;
use Academy\Traits\AuditableAcademyModel;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyAssessment extends BaseModel
{
    use AuditableAcademyModel;
    use HasRefid;

    public const TABLE = 'academy_assessment';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'asm_';

    protected array $fillable = [
        'refid',
        'lesson_id',
        'type',
        'passing_score',
        'attempts_allowed',
        'time_limit',
        'shuffle_questions',
        'show_correct_answers',
        'late_policy',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'lesson_id' => 'integer',
        'type' => AssessmentType::class,
        'passing_score' => 'integer',
        'attempts_allowed' => 'integer',
        'time_limit' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_correct_answers' => 'boolean',
        'late_policy' => 'array',
        'metadata' => 'json',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AcademyQuestion::class, 'assessment_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AcademySubmission::class, 'assessment_id');
    }
}
