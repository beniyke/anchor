<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Notification sent when a new invoice is generated.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Notifications;

use Mail\Core\EmailComponent;

class InvoiceGeneratedNotification extends WaveNotification
{
    public function getSubject(): string
    {
        $invoiceNumber = $this->payload->get('invoice_number');

        return "Invoice Generated - #{$invoiceNumber}";
    }

    protected function getRawMessageContent(): string
    {
        $name = $this->payload->get('name') ?: 'Customer';
        $invoiceNumber = $this->payload->get('invoice_number');
        $amount = $this->payload->get('amount');
        $dueDate = $this->payload->get('due_date');
        $viewUrl = $this->payload->get('view_url');

        return EmailComponent::make()
            ->greeting("Hello {$name},")
            ->line("A new invoice has been generated for your subscription.")
            ->table([
                'Invoice Number' => $invoiceNumber,
                'Amount Due' => $amount,
                'Due Date' => $dueDate,
            ])
            ->line("You can view your invoice by clicking the button below:")
            ->action('View Invoice', url($viewUrl))
            ->line("If you have any questions, please contact our support team.")
            ->render();
    }
}
