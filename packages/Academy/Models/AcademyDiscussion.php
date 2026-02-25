<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\DiscussionType;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyDiscussion extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_discussion';

    protected string $refidPrefix = 'dsc_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'user_id',
        'program_id',
        'lesson_id',
        'parent_id',
        'type',
        'content',
        'is_resolved',
        'is_pinned',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'user_id' => 'integer',
        'program_id' => 'integer',
        'lesson_id' => 'integer',
        'parent_id' => 'integer',
        'type' => DiscussionType::class,
        'is_resolved' => 'boolean',
        'is_pinned' => 'boolean',
        'metadata' => 'json',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
