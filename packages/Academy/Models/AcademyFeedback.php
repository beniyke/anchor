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

class AcademyFeedback extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_feedback';

    protected string $refidPrefix = 'fdb_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'submission_id',
        'user_id',
        'question_id',
        'comment',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'submission_id' => 'integer',
        'user_id' => 'integer',
        'question_id' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AcademySubmission::class, 'submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AcademyQuestion::class, 'question_id');
    }

    // User relationship external
}
