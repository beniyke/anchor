<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\LessonType;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Database\Traits\HasRefid;

class AcademyLesson extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_lesson';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'les_';

    protected array $fillable = [
        'refid',
        'module_id',
        'title',
        'slug',
        'type',
        'content',
        'duration',
        'sort_order',
        'is_preview',
        'is_optional',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'module_id' => 'integer',
        'type' => LessonType::class,
        'is_preview' => 'boolean',
        'is_optional' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(AcademyModule::class, 'module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(AcademyResource::class, 'lesson_id');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(AcademyAssessment::class, 'lesson_id');
    }

    public function liveSession(): HasOne
    {
        return $this->hasOne(AcademyLiveSession::class, 'lesson_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(AcademyProgress::class, 'lesson_id');
    }
}
