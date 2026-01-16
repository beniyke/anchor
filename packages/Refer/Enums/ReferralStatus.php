<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Referral status enum.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Enums;

enum ReferralStatus: string
{
    case PENDING = 'pending';
    case QUALIFIED = 'qualified';
    case REWARDED = 'rewarded';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
}
