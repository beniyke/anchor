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

class AcademyAnnouncement extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_announcement';

    protected string $refidPrefix = 'ann_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'program_id',
        'user_id',
        'title',
        'content',
        'send_email',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'program_id' => 'integer',
        'user_id' => 'integer',
        'send_email' => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }
}
