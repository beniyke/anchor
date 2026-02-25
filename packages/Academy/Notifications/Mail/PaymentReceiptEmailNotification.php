<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class PaymentReceiptEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Payment Receipt - " . $this->payload->get('program_title');
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->status("Payment Successful", 'success')
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Thank you for your payment of **" . $this->payload->get('amount_formatted') . "** for **" . $this->payload->get('program_title') . "**.")
            ->divider()
            ->attributes([
                'Reference' => $this->payload->get('reference'),
                'Date' => $this->payload->get('date'),
            ])
            ->action("View Transaction", url($this->payload->get('url')))
            ->render();
    }
}
