<?php

declare(strict_types=1);

namespace Hub\Schedules;

use Cron\Interfaces\Schedulable;
use Cron\Schedule;

class HubSchedule implements Schedulable
{
    /**
     * Define the schedule for the task.
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->task()
            ->signature('hub:remind')
            ->everyThirtyMinutes();
    }
}
