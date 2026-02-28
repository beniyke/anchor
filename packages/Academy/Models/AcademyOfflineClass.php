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
use Database\Relations\MorphMany;
use Database\Traits\HasRefid;

class AcademyOfflineClass extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_offline_class';

    protected string $refidPrefix = 'off_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'lesson_id',
        'location',
        'starts_at',
        'ends_at',
        'notes',
        'status',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'lesson_id' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(AcademyAttendance::class, 'attendable');
    }
}
