<?php

declare(strict_types=1);

namespace Onboard\Enums;

enum EquipmentStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case DELIVERED = 'delivered';
    case RETURNED = 'returned';
}
