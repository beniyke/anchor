<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Segment type enum.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Enums;

enum SegmentType: string
{
    case USER_ID = 'user_id';
    case ROLE = 'role';
    case PLAN = 'plan';
    case EMAIL_DOMAIN = 'email_domain';
    case LOCATION = 'location';
}
