<?php

declare(strict_types=1);

namespace Wave\Services;

/**
 * Anchor Framework
 *
 * Proxy class for fluent historical analytics retrieval.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */
class AnalyticsHistoryProxyService
{
    protected AnalyticsManagerService $manager;

    public function __construct(AnalyticsManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function revenue(string|array $range = '30d'): array
    {
        return $this->manager->getHistory('revenue', $range);
    }

    public function newSubscriptions(string|array $range = '30d'): array
    {
        return $this->manager->getHistory('new_subscriptions', $range);
    }

    public function cancellations(string|array $range = '30d'): array
    {
        return $this->manager->getHistory('cancellations', $range);
    }
}
