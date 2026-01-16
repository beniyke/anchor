<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fee Type
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Enums;

enum FeeType: string
{
    case FIXED = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';
    case TIERED = 'TIERED';
}
