<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;

class AcademyAnswer extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_answer';

    protected string $refidPrefix = 'ans_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'submission_id',
        'question_id',
        'choice_id',
        'content',
        'file_path',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'submission_id' => 'integer',
        'question_id' => 'integer',
        'choice_id' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AcademySubmission::class, 'submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AcademyQuestion::class, 'question_id');
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(AcademyChoice::class, 'choice_id');
    }
}
