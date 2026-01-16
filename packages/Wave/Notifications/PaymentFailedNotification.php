<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a payment fails.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Notifications;

use Mail\Core\EmailComponent;

class PaymentFailedNotification extends WaveNotification
{
    public function getSubject(): string
    {
        $invoiceNumber = $this->payload->get('invoice_number');

        return "Payment Failed - #{$invoiceNumber}";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name') ?: 'Customer';
        $invoiceNumber = $this->payload->get('invoice_number');
        $updateUrl = $this->payload->get('update_url');

        return EmailComponent::make()
            ->status("Payment Failed", 'error')
            ->greeting("Hello {$name},")
            ->line("We were unable to process your payment for invoice #{$invoiceNumber}.")
            ->line("Please update your payment method to avoid service interruption.")
            ->action('Update Payment Method', url($updateUrl))
            ->render();
    }
}
