<?php

declare(strict_types=1);

namespace Blish\Schedules;

use Cron\Interfaces\Schedulable;
use Cron\Schedule;

class BlishSchedule implements Schedulable
{
    /**
     * Define the schedule for the task.
     */
    public function schedule(Schedule $schedule): void
    {
        $schedule->task()
            ->signature('blish:process')
            ->hourly();
    }
}
