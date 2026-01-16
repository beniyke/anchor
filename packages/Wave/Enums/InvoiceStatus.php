<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Invoice Status Enum
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'open';
    case PAID = 'paid';
    case VOID = 'void';
    case UNCOLLECTIBLE = 'uncollectible';
}
