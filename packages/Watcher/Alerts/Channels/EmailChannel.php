<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Email delivery channel for Watcher alerts.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Alerts\Channels;

use Helpers\Data;
use Mail\Mail;
use Watcher\Alerts\Mail\WatcherAlert;

class EmailChannel
{
    private array $recipients;

    public function __construct(array $recipients)
    {
        $this->recipients = $recipients;
    }

    public function send(string $type, array $data): void
    {
        $emails = $this->recipients;
        $payload = Data::make(compact('type', 'data', 'emails'));

        Mail::send(new WatcherAlert($payload));
    }
}
