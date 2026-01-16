<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Blish Analytics Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Services;

use Blish\Models\Campaign;
use Blish\Models\Event;
use Blish\Models\Stat;
use Blish\Models\Subscriber;

class BlishAnalyticsService
{
    public function recordOpen(Subscriber $subscriber, Campaign $campaign, array $metadata = []): void
    {
        Event::create([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
            'type' => 'open',
            'metadata' => $metadata,
        ]);

        $this->incrementStat($campaign, 'opens');
    }

    public function recordClick(Subscriber $subscriber, Campaign $campaign, string $url, array $metadata = []): void
    {
        Event::create([
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
            'type' => 'click',
            'metadata' => array_merge($metadata, ['url' => $url]),
        ]);

        $this->incrementStat($campaign, 'clicks');
    }

    protected function incrementStat(Campaign $campaign, string $column): void
    {
        $stat = Stat::firstOrCreate(
            ['campaign_id' => $campaign->id],
            ['opens' => 0, 'clicks' => 0, 'sent' => 0, 'delivered' => 0, 'failed' => 0]
        );

        $stat->increment($column);
    }

    public function recordSent(Campaign $campaign, int $count = 1): void
    {
        $stat = Stat::firstOrCreate(
            ['campaign_id' => $campaign->id],
            ['opens' => 0, 'clicks' => 0, 'sent' => 0, 'delivered' => 0, 'failed' => 0]
        );

        $stat->increment('sent', $count);
    }
}
