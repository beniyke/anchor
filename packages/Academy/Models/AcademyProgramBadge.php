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

class AcademyProgramBadge extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_program_badge';

    protected string $refidPrefix = 'pbg_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'program_id',
        'badge_id',
        'trigger_type',
        'trigger_value',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'program_id' => 'integer',
        'badge_id' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(AcademyBadge::class, 'badge_id');
    }
}
