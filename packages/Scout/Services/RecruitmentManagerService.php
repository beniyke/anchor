<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Recruitment Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services;

use App\Models\User;
use Audit\Audit;
use Link\Link;
use Scout\Models\Application;
use Scout\Models\Interview;
use Scout\Models\Offer;
use Scout\Models\Stage;
use Slot\Slot;

class RecruitmentManagerService
{
    /**
     * Advance an application to the next stage.
     */
    public function advance(Application $application, Stage $stage): void
    {
        $oldStage = $application->stage;
        $application->update(['scout_stage_id' => $stage->id]);

        if (class_exists('Audit\Audit')) {
            Audit::log('scout.application.advanced', [
                'id' => $application->id,
                'from' => $oldStage->name,
                'to' => $stage->name
            ], $application);
        }

        // Potential handoff to Onboard if stage matches 'hired'
        if ($stage->slug === 'hired') {
            $this->handoffToOnboard($application);
        }
    }

    public function reject(Application $application, string $reason): void
    {
        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $reason
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('scout.application.rejected', ['id' => $application->id, 'reason' => $reason], $application);
        }
    }

    /**
     * Schedule an interview.
     */
    public function scheduleInterview(Application $application, User $interviewer, array $data): Interview
    {
        $interview = Interview::create([
            'scout_application_id' => $application->id,
            'user_id' => $interviewer->id,
            'scheduled_at' => $data['scheduled_at'],
            'duration' => $data['duration'] ?? 30,
            'location' => $data['location'] ?? null,
            'status' => 'scheduled'
        ]);

        if (isset($data['slot_id']) && class_exists('Slot\Slot')) {
            // Logic to mark slot as booked in Slot package
            $interview->update(['slot_id' => $data['slot_id']]);
        }

        return $interview;
    }

    public function createOffer(Application $application, float $amount, array $data): Offer
    {
        $offer = Offer::create([
            'scout_application_id' => $application->id,
            'amount' => $amount,
            'currency' => $data['currency'] ?? 'USD',
            'terms' => $data['terms'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => 'pending',
        ]);

        if (class_exists('Link\Link')) {
            $link = Link::make()
                ->for($offer)
                ->expiresAt($offer->expires_at)
                ->create();

            $offer->update(['link_id' => $link->id]);
        }

        return $offer;
    }

    /**
     * Handoff to Onboard package.
     */
    protected function handoffToOnboard(Application $application): void
    {
        if (class_exists('Onboard\Onboard')) {
        }
    }
}
