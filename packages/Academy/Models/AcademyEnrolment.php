<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\EnrolmentStatus;
use Academy\Traits\AuditableAcademyModel;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Database\Traits\HasRefid;

class AcademyEnrolment extends BaseModel
{
    use AuditableAcademyModel;
    use HasRefid;

    public const TABLE = 'academy_enrolment';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'enr_';

    protected array $fillable = [
        'refid',
        'program_id',
        'user_id',
        'payment_plan_id',
        'status',
        'enrolled_at',
        'completed_at',
        'expires_at',
        'progress_percent',
        'admission_id',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'program_id' => 'integer',
        'user_id' => 'integer',
        'payment_plan_id' => 'integer',
        'status' => EnrolmentStatus::class,
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'progress_percent' => 'integer',
        'metadata' => 'array',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(AcademyPaymentPlan::class, 'payment_plan_id');
    }

    public function lessonsProgress(): HasMany
    {
        return $this->hasMany(AcademyProgress::class, 'enrolment_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AcademyNote::class, 'enrolment_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(AcademyCertificate::class, 'enrolment_id');
    }

    public function instalments(): HasMany
    {
        return $this->hasMany(AcademyInstalment::class, 'enrolment_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AcademySubmission::class, 'enrolment_id');
    }
}
