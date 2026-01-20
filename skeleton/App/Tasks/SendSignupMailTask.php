<?php

declare(strict_types=1);

/**
 * Class SendSignupMailTask handles the execution of a specific task and provides
 * feedback messages on success or failure.
 */

namespace App\Tasks;

use App\Notifications\Email\AccountVerificationEmailNotification;
use Mail\Mail;
use Queue\BaseTask;
use Queue\Scheduler;

class SendSignupMailTask extends BaseTask
{
    /**
     * Defines the occurrence frequency of the task.
     *
     * @return string Task occurrence frequency: once() or always()
     */
    public function occurrence(): string
    {
        return self::once();
    }

    /**
     * Defines the scheduling period for the task.
     * It can be day, minute, month, year, hour, etc.
     * For example, the following sets a 1-minute interval.
     */
    public function period(Scheduler $schedule): Scheduler
    {
        return $schedule->minute(1);
    }

    /**
     * Provides a success message when the task is executed successfully.
     */
    protected function successMessage(): string
    {
        return 'Task successfully executed.';
    }

    /**
     * Provides a failure message when the task fails to execute.
     */
    protected function failedMessage(): string
    {
        return 'Task execution failed.';
    }

    /**
     * Defines the task logic using the payload passed in the constructor.
     * This is where you implement the core functionality of the task.
     */
    protected function execute(): bool
    {
        if ($this->payload) {
            Mail::send(new AccountVerificationEmailNotification($this->payload));

            return true;
        }

        return false;
    }
}
