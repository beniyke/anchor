<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\PaymentStatus;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyInstalment extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_instalment';

    protected string $refidPrefix = 'ins_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'enrolment_id',
        'amount',
        'sequence',
        'due_at',
        'paid_at',
        'payment_reference',
        'status',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'enrolment_id' => 'integer',
        'amount' => 'integer',
        'sequence' => 'integer',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'status' => PaymentStatus::class,
    ];

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(AcademyEnrolment::class, 'enrolment_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(AcademyPaymentReminder::class, 'instalment_id');
    }
}
