<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\CertificateStatus;
use Academy\Traits\AuditableAcademyModel;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;

class AcademyCertificate extends BaseModel
{
    use AuditableAcademyModel;
    use HasRefid;

    public const TABLE = 'academy_certificate';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'crt_';

    protected array $fillable = [
        'refid',
        'enrolment_id',
        'certificate_number',
        'file_path',
        'issued_at',
        'expires_at',
        'status',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'enrolment_id' => 'integer',
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'status' => CertificateStatus::class,
        'metadata' => 'array',
    ];

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(AcademyEnrolment::class, 'enrolment_id');
    }
}
