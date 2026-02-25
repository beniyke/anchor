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

class AcademyChoice extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_choice';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'cho_';

    protected array $fillable = [
        'refid',
        'question_id',
        'text',
        'is_correct',
    ];

    protected array $casts = [
        'id' => 'integer',
        'question_id' => 'integer',
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AcademyQuestion::class, 'question_id');
    }
}
