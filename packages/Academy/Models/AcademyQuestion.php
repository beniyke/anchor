<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\QuestionType;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyQuestion extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_question';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'que_';

    protected array $fillable = [
        'refid',
        'assessment_id',
        'type',
        'text',
        'explanation',
        'points',
        'sort_order',
    ];

    protected array $casts = [
        'id' => 'integer',
        'assessment_id' => 'integer',
        'type' => QuestionType::class,
        'points' => 'integer',
        'sort_order' => 'integer',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(AcademyAssessment::class, 'assessment_id');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(AcademyChoice::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AcademyAnswer::class, 'question_id');
    }
}
