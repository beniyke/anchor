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

class AcademyAttendance extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_attendance';

    protected string $refidPrefix = 'att_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'live_session_id',
        'enrolment_id',
        'joined_at',
        'left_at',
        'duration',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'live_session_id' => 'integer',
        'enrolment_id' => 'integer',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'duration' => 'integer',
    ];

    public function liveSession(): BelongsTo
    {
        return $this->belongsTo(AcademyLiveSession::class, 'live_session_id');
    }

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(AcademyEnrolment::class, 'enrolment_id');
    }
}
