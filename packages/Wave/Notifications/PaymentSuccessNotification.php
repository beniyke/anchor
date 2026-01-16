<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a payment is successful.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Notifications;

use Mail\Core\EmailComponent;

class PaymentSuccessNotification extends WaveNotification
{
    public function getSubject(): string
    {
        $invoiceNumber = $this->payload->get('invoice_number');

        return "Payment Successful - #{$invoiceNumber}";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name') ?: 'Customer';
        $invoiceNumber = $this->payload->get('invoice_number');
        $viewUrl = $this->payload->get('view_url');

        return EmailComponent::make()
            ->status("Payment Successful", 'success')
            ->greeting("Hello {$name},")
            ->line("Thank you for your payment! Your transaction for invoice #{$invoiceNumber} was successful.")
            ->line("Your subscription is now active and up to date.")
            ->action('View Invoice', url($viewUrl))
            ->render();
    }
}
