<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Reward Partner Referral.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Listeners;

use Ally\Ally;
use Refer\Events\ReferralCompletedEvent;

class RewardPartnerReferral
{
    public function handle(ReferralCompletedEvent $event): void
    {
        if ($event->tag === 'become-partner') {
            // Find the referrer's reseller profile
            $referrer = Ally::findByUser($event->referrer->id);

            if ($referrer) {
                // Give bonus credits
                Ally::addCredits($referrer, 1000);
            }
        }
    }
}
