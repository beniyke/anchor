<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Type
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Enums;

enum TransactionType: string
{
    case CREDIT = 'CREDIT';
    case DEBIT = 'DEBIT';
    case REFUND = 'REFUND';
    case TRANSFER_IN = 'TRANSFER_IN';
    case TRANSFER_OUT = 'TRANSFER_OUT';
}
