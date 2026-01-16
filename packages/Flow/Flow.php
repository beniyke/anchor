<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Flow
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow;

use Flow\Services\CollaborationService;
use Flow\Services\ProjectService;
use Flow\Services\RecurringTaskService;
use Flow\Services\ReminderService;
use Flow\Services\ReportingService;
use Flow\Services\TaskService;

class Flow
{
    public static function projects(): ProjectService
    {
        return resolve(ProjectService::class);
    }

    public static function tasks(): TaskService
    {
        return resolve(TaskService::class);
    }

    public static function collaboration(): CollaborationService
    {
        return resolve(CollaborationService::class);
    }

    public static function recurring(): RecurringTaskService
    {
        return resolve(RecurringTaskService::class);
    }

    public static function reports(): ReportingService
    {
        return resolve(ReportingService::class);
    }

    public static function reminders(): ReminderService
    {
        return resolve(ReminderService::class);
    }
}
