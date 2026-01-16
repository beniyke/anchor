<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Slack delivery channel for Watcher alerts.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Alerts\Channels;

use Helpers\Http\Client\Curl;

class SlackChannel
{
    private string $webhookUrl;

    public function __construct(string $webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function send(string $type, array $data): void
    {
        $message = $this->formatMessage($type, $data);

        $this->newCurl()->post($this->webhookUrl, $message)->asJson()->send();
    }

    protected function newCurl(): Curl
    {
        return new Curl();
    }

    private function formatMessage(string $type, array $data): array
    {
        $color = match ($type) {
            'error_rate' => 'danger',
            'slow_query', 'slow_request' => 'warning',
            default => 'good',
        };

        $text = $this->getText($type, $data);
        $fields = $this->getFields($data);

        return [
            'attachments' => [
                [
                    'color' => $color,
                    'title' => "Watcher Alert: {$type}",
                    'text' => $text,
                    'fields' => $fields,
                    'footer' => 'Watcher Monitoring',
                    'ts' => time(),
                ],
            ],
        ];
    }

    private function getText(string $type, array $data): string
    {
        return match ($type) {
            'error_rate' => "Error rate is {$data['error_rate']}% (threshold: {$data['threshold']}%)",
            'slow_query' => "Detected {$data['count']} slow queries",
            'slow_request' => "Average response time is {$data['avg_response_time_ms']}ms",
            default => json_encode($data),
        };
    }

    private function getFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (! is_array($value)) {
                $fields[] = [
                    'title' => ucwords(str_replace('_', ' ', $key)),
                    'value' => (string) $value,
                    'short' => true,
                ];
            }
        }

        return $fields;
    }
}
