<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Models\AcademyBadgeAward;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyProgramBadge;

class BadgeService
{
    /**
     * Award a badge to a learner.
     */
    public function award(int $userId, int $badgeId, ?int $programId = null): AcademyBadgeAward
    {
        return AcademyBadgeAward::updateOrCreate(
            ['user_id' => $userId, 'badge_id' => $badgeId, 'program_id' => $programId]
        );
    }

    public function checkTriggers(AcademyEnrolment $enrolment, string $triggerType): void
    {
        $programBadges = AcademyProgramBadge::where('program_id', $enrolment->program_id)
            ->where('trigger_type', $triggerType)
            ->get();

        foreach ($programBadges as $pb) {
            $this->award((int) $enrolment->user_id, (int) $pb->badge_id, (int) $enrolment->program_id);
        }
    }
}
