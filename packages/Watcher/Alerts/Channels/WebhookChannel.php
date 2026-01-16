<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Webhook delivery channel for Watcher alerts.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Alerts\Channels;

use Helpers\DateTimeHelper;
use Helpers\Http\Client\Curl;

class WebhookChannel
{
    private string $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function send(string $type, array $data): void
    {
        $payload = [
            'alert_type' => $type,
            'timestamp' => DateTimeHelper::now()->toDateTimeString(),
            'data' => $data,
        ];

        $this->newCurl()->post($this->webhookUrl, compact('payload'))->send();
    }

    protected function newCurl(): Curl
    {
        return new Curl();
    }
}
