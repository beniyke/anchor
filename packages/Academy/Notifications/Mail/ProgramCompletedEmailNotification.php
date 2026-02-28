<?php

declare(strict_types=1);

namespace Academy\Notifications\Mail;

use Mail\Core\EmailComponent;

class ProgramCompletedEmailNotification extends AcademyEmailNotification
{
    public function getSubject(): string
    {
        return "Congratulations on completing " . $this->payload->get('program_title');
    }

    protected function getRawMessageContent(): string
    {
        return EmailComponent::make()
            ->greeting("Goal Achieved!")
            ->greeting("Hello " . $this->payload->get('name') . ",")
            ->markdown("Well done! You have successfully completed **" . $this->payload->get('program_title') . "**. We are proud of your achievement.")
            ->action("Rate this Program", url($this->payload->get('url')))
            ->render();
    }
}
