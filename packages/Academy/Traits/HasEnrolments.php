<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Traits;

use Academy\Models\AcademyCertificate;
use Academy\Models\AcademyEnrolment;
use Database\Relations\HasMany;

trait HasEnrolments
{
    public function enrolments(): HasMany
    {
        return $this->hasMany(AcademyEnrolment::class, 'user_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(AcademyCertificate::class, 'user_id');
    }

    public function isEnrolledIn(int $programId): bool
    {
        return AcademyEnrolment::where('user_id', $this->id)
            ->where('program_id', $programId)
            ->where('status', 'active')
            ->exists();
    }
}
