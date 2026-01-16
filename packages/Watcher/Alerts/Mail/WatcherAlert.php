<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Mail notification class for Watcher alerts.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Alerts\Mail;

use Helpers\DateTimeHelper;
use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class WatcherAlert extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => $this->payload->get('emails')
        ];
    }

    public function getSubject(): string
    {
        $type = $this->payload->get('type');
        $data = $this->payload->get('data');

        return match ($type) {
            'error_rate' => "⚠️ High Error Rate Alert: {$data['error_rate']}%",
            'slow_query' => "🐌 Slow Query Alert: {$data['slowest']['duration_ms']}ms",
            'slow_request' => "⏱️ Slow Request Alert: {$data['avg_response_time_ms']}ms",
            default => "Watcher Alert: {$type}",
        };
    }

    public function getPreheader(): string
    {
        $type = $this->payload->get('type');
        $data = $this->payload->get('data');

        return match ($type) {
            'error_rate' => "Alert triggered. Error rate is currently {$data['error_rate']}%, exceeding the threshold.",
            'slow_query' => "Performance drop: A query took {$data['slowest']['duration_ms']}ms to execute.",
            'slow_request' => "Traffic warning: Average response time has climbed to {$data['avg_response_time_ms']}ms.",
            default => "New monitoring alert received from Watcher regarding {$type}.",
        };
    }

    public function getTitle(): string
    {
        return 'Watcher Alert';
    }

    protected function getRawMessageContent(): string
    {
        $type = $this->payload->get('type');
        $data = $this->payload->get('data');

        $html = "<h2>Watcher Alert: " . str_replace('_', ' ', ucfirst($type)) . "</h2>";
        $html .= '<p><strong>Time:</strong> ' . DateTimeHelper::now()->format('Y-m-d H:i:s') . '</p>';
        $html .= '<h3>Details:</h3>';
        $html .= '<ul>';

        foreach ($data as $key => $value) {
            $label = str_replace('_', ' ', ucfirst((string) $key));
            if (is_array($value)) {
                $html .= "<li><strong>{$label}:</strong> <pre>" . json_encode($value, JSON_PRETTY_PRINT) . '</pre></li>';
            } else {
                $html .= "<li><strong>{$label}:</strong> {$value}</li>";
            }
        }

        $html .= '</ul>';

        return EmailComponent::make(false)
            ->raw($html)
            ->render();
    }
}
