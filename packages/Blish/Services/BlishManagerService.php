<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Blish Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Services;

use Blish\Models\Campaign;
use Blish\Models\Subscriber;
use Blish\Services\Builders\CampaignBuilder;
use Blish\Services\Builders\SubscriberBuilder;
use Helpers\String\Str;

class BlishManagerService
{
    public function subscriber(): SubscriberBuilder
    {
        return new SubscriberBuilder($this);
    }

    public function campaign(): CampaignBuilder
    {
        return new CampaignBuilder($this);
    }

    public function processCampaign(Campaign $campaign): void
    {
        resolve(CampaignProcessorService::class)->process($campaign);
    }

    public function analytics(): BlishAnalyticsService
    {
        return resolve(BlishAnalyticsService::class);
    }

    public function subscribe(string $email, array $data = []): Subscriber
    {
        return $this->subscriber()
            ->email($email)
            ->data($data)
            ->save();
    }

    public function unsubscribe(string $email): void
    {
        $subscriber = $this->find($email);
        if ($subscriber) {
            $subscriber->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function find(string $email): ?Subscriber
    {
        return Subscriber::query()->where('email', $email)->first();
    }

    public function persistSubscriber(array $data): Subscriber
    {
        $subscriber = $this->find($data['email']);

        if ($subscriber) {
            $subscriber->update($data);

            return $subscriber;
        }

        $data['refid'] = Str::random('alnum', 32);

        return Subscriber::create($data);
    }
}
