<?php

declare(strict_types=1);

namespace Scout\Schedules;

use Cron\Interfaces\Schedulable;
use Cron\Schedule;

class InterviewReminderSchedule implements Schedulable
{
    /**
     * Define the schedule for the task.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->task()
            ->signature('scout:reminders')
            ->everyThirtyMinutes();
    }
}
