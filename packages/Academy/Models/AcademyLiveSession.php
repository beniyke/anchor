<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\SessionStatus;
use Academy\Enums\VideoProvider;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyLiveSession extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_live_session';

    protected string $refidPrefix = 'liv_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'lesson_id',
        'provider',
        'meeting_id',
        'meeting_url',
        'meeting_password',
        'starts_at',
        'ends_at',
        'status',
        'recording_url',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'lesson_id' => 'integer',
        'provider' => VideoProvider::class,
        'status' => SessionStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(AcademyAttendance::class, 'live_session_id');
    }
}
