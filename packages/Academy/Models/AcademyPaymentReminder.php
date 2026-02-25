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

class AcademyPaymentReminder extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_payment_reminder';

    protected string $refidPrefix = 'prm_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'instalment_id',
        'sent_at',
        'type',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'instalment_id' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function instalment(): BelongsTo
    {
        return $this->belongsTo(AcademyInstalment::class, 'instalment_id');
    }
}
