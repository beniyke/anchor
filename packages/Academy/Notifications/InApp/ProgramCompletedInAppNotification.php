<?php

declare(strict_types=1);

namespace Academy\Notifications\InApp;

class ProgramCompletedInAppNotification extends AcademyInAppNotification
{
    public function getMessage(): string
    {
        return "Congratulations! You've completed " . $this->payload->get('program_title') . ".";
    }
}
