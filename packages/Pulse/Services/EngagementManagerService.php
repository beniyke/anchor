<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Engagement Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services;

use App\Models\User;
use Audit\Audit;
use Pulse\Models\Badge;
use Pulse\Models\Reputation;

class EngagementManagerService
{
    /**
     * Award reputation points to a user.
     */
    public function awardPoints(User $user, int $points): void
    {
        $reputation = Reputation::firstOrCreate(['user_id' => $user->id], ['points' => 0]);
        $reputation->increment('points', $points);

        if (class_exists('Audit\Audit')) {
            Audit::log('pulse.points.awarded', ['user_id' => $user->id, 'points' => $points]);
        }
    }

    public function awardBadge(User $user, Badge $badge): void
    {
        $badge->users()->syncWithoutDetaching([$user->id]);

        if (class_exists('Audit\Audit')) {
            Audit::log('pulse.badge.awarded', ['user_id' => $user->id, 'badge_id' => $badge->id]);
        }
    }
}
