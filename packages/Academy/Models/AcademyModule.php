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
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyModule extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_module';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'mod_';

    protected array $fillable = [
        'refid',
        'program_id',
        'title',
        'description',
        'sort_order',
        'is_locked',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'program_id' => 'integer',
        'is_locked' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(AcademyLesson::class, 'module_id');
    }
}
