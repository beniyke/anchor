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

class AcademyGrade extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_grade';

    protected string $refidPrefix = 'grd_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'submission_id',
        'graded_by',
        'raw_score',
        'percent_score',
        'is_passing',
        'graded_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'submission_id' => 'integer',
        'raw_score' => 'integer',
        'percent_score' => 'integer',
        'is_passing' => 'boolean',
        'graded_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AcademySubmission::class, 'submission_id');
    }

    // Role relationship to User graded_by would be external
}
