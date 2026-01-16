<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Status
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Enums;

enum TransactionStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case REVERSED = 'REVERSED';
}
