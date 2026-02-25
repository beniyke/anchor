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

class AcademyWaitlist extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_waitlist';

    protected string $refidPrefix = 'wai_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'program_id',
        'user_id',
        'email',
        'status',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'program_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }
}
