<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Schedules;

use Cron\Interfaces\Schedulable;
use Cron\Schedule;

class AcademySchedule implements Schedulable
{
    /**
     * Register scheduled tasks for the Academy package.
     */
    public function schedule(Schedule $schedule): void
    {
        // Daily: Check for overdue instalments
        $schedule->command('academy:payments:sync')->daily();

        // Hourly: Prune stale waitlists or expired enrolments
        $schedule->command('academy:prune:expired')->hourly();
    }
}
