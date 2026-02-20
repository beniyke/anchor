<?php

declare(strict_types=1);

namespace Wave\Schedules;

use Cron\Interfaces\Schedulable;
use Cron\Schedule;

class SubscriptionRenewalSchedule implements Schedulable
{
    /**
     * Define the schedule for the tasks.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->task()
            ->signature('wave:renew')
            ->hourly();

        $schedule->task()
            ->signature('wave:renewal-reminders')
            ->daily();
    }
}
