<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Campaign Processor Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Services;

use Blish\Models\Campaign;
use Blish\Models\Subscriber;
use Blish\Notifications\CampaignEmailNotification;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Link\Link;
use Mail\Mail;

class CampaignProcessorService
{
    public function __construct(
        protected BlishManagerService $manager,
        protected BlishAnalyticsService $analytics
    ) {
    }

    public function processScheduled(): int
    {
        $campaigns = Campaign::query()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', DateTimeHelper::now()->toDateTimeString())
            ->get();

        foreach ($campaigns as $campaign) {
            $this->process($campaign);
        }

        return count($campaigns);
    }

    public function process(Campaign $campaign): void
    {
        if ($campaign->status !== 'scheduled' && $campaign->status !== 'draft') {
            return;
        }

        $campaign->update(['status' => 'sending']);

        // In a real app, this would be queued or batched.
        // For now, we'll process active subscribers.
        $subscribers = Subscriber::query()->where('status', 'active')->get();

        foreach ($subscribers as $subscriber) {
            $this->sendToSubscriber($campaign, $subscriber);
        }

        $campaign->update([
            'status' => 'sent',
            'sent_at' => DateTimeHelper::now()->toDateTimeString(),
        ]);
    }

    protected function sendToSubscriber(Campaign $campaign, Subscriber $subscriber): void
    {
        $unsubscribeUrl = null;
        if (class_exists(Link::class)) {
            $link = Link::create([
                'linkable_type' => get_class($subscriber),
                'linkable_id' => $subscriber->id,
                'scopes' => ['newsletter.unsubscribe'],
                'metadata' => ['campaign_refid' => $campaign->refid],
            ]);
            $unsubscribeUrl = Link::generateSignedUrl($link, url('/blish/unsubscribe'));
        }

        $payload = Data::make([
            'email' => $subscriber->email,
            'name' => $subscriber->name,
            'subject' => $campaign->subject,
            'content' => $campaign->template ? $campaign->template->content : ($campaign->metadata['content'] ?? ''),
            'unsubscribe_url' => $unsubscribeUrl,
        ]);

        Mail::send(new CampaignEmailNotification($payload));

        $this->analytics->recordSent($campaign);

        // Log delivery event
        $campaign->events()->create([
            'subscriber_id' => $subscriber->id,
            'event_type' => 'delivered',
            'metadata' => [
                'email' => $subscriber->email,
                'sent_at' => DateTimeHelper::now()->toDateTimeString(),
            ],
        ]);
    }
}
