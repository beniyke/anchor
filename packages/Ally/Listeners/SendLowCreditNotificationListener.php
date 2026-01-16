<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Listener to send LowCreditNotification when LowCreditEvent is fired.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Listeners;

use Ally\Events\LowCreditEvent;
use Ally\Notifications\LowCreditNotification;
use Helpers\Data;
use Mail\Mail;

class SendLowCreditNotificationListener
{
    public function handle(LowCreditEvent $event): void
    {
        $reseller = $event->reseller;
        $email = $reseller->user?->email ?? null;

        if ($email) {
            Mail::send(new LowCreditNotification(Data::make([
                'name' => $reseller->user->name ?? $reseller->company_name,
                'email' => $email,
                'company_name' => $reseller->company_name,
                'balance' => $event->balance,
                'threshold' => $event->threshold,
                'dashboard_url' => config('ally.urls.dashboard', 'partner/dashboard'),
            ])));
        }
    }
}
