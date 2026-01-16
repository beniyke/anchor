<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Referral Completed Event.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Events;

use Core\Event;
use Refer\Models\Referral;

class ReferralCompletedEvent extends Event
{
    /**
     * The referral instance.
     */
    public Referral $referral;

    /**
     * The tag associated with the referral links (optional).
     */
    public ?string $tag;

    /**
     * The user who referred.
     */
    public $referrer;

    /**
     * Create a new event instance.
     */
    public function __construct(Referral $referral)
    {
        $this->referral = $referral;
        $this->referrer = $referral->referrer; // Assuming relationship exists
        // Placeholder for tag implementation or derivation
        $this->tag = $referral->code->tag ?? null;
    }
}
