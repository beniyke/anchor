<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Currency
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Enums;

enum Currency: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case NGN = 'NGN';
    case KES = 'KES';
    case ZAR = 'ZAR';
    case GHS = 'GHS';
}
