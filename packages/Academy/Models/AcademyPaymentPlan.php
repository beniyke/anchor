<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\PaymentPlanType;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyPaymentPlan extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_payment_plan';

    protected string $refidPrefix = 'ppl_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'program_id',
        'name',
        'type',
        'price',
        'currency',
        'instalment_count',
        'instalment_interval',
        'is_active',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'program_id' => 'integer',
        'type' => PaymentPlanType::class,
        'price' => 'integer',
        'instalment_count' => 'integer',
        'instalment_interval' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(AcademyEnrolment::class, 'payment_plan_id');
    }
}
