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

class AcademyProgress extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_progress';

    protected string $refidPrefix = 'pgs_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'enrolment_id',
        'lesson_id',
        'completed_at',
        'time_spent',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'enrolment_id' => 'integer',
        'lesson_id' => 'integer',
        'completed_at' => 'datetime',
        'time_spent' => 'integer',
        'metadata' => 'array',
    ];

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(AcademyEnrolment::class, 'enrolment_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }
}
