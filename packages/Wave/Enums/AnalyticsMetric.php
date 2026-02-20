<?php

declare(strict_types=1);

namespace Wave\Enums;

enum AnalyticsMetric: string
{
    case REVENUE = 'revenue';
    case NEW_SUBSCRIPTIONS = 'new_subscriptions';
    case CANCELLATIONS = 'cancellations';
}
