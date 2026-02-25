<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\ProgramStatus;
use Academy\Traits\AuditableAcademyModel;
use Database\BaseModel;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyProgram extends BaseModel
{
    use AuditableAcademyModel;
    use HasRefid;

    protected string $table = 'academy_program';

    protected string $refidPrefix = 'prg_';

    protected array $fillable = [
        'refid',
        'title',
        'slug',
        'description',
        'content',
        'thumbnail',
        'banner',
        'status',
        'is_featured',
        'is_private',
        'access_code',
        'certificate_template',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'status' => ProgramStatus::class,
        'is_featured' => 'boolean',
        'is_private' => 'boolean',
        'metadata' => 'array',
    ];

    public array $attributes = [
        'status' => 'draft',
    ];

    public function modules(): HasMany
    {
        return $this->hasMany(AcademyModule::class, 'program_id');
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(AcademyEnrolment::class, 'program_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(AcademyProgramMember::class, 'program_id');
    }

    public function paymentPlans(): HasMany
    {
        return $this->hasMany(AcademyPaymentPlan::class, 'program_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(AcademyAnnouncement::class, 'program_id');
    }
}
