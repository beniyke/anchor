<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reminder Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use Core\Services\ConfigServiceInterface;
use Flow\Models\Reminder;
use Flow\Notifications\TaskReminderNotification;
use Flow\Services\Builders\ReminderBuilder;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Mail\Mail;

class ReminderService
{
    public function __construct(
        protected ConfigServiceInterface $config
    ) {
    }

    public function make(): ReminderBuilder
    {
        return new ReminderBuilder($this);
    }

    public function create(array $data): Reminder
    {
        $reminder = new Reminder();
        $reminder->fill($data);
        $reminder->save();

        return $reminder;
    }

    public function processReminders(): int
    {
        $now = DateTimeHelper::now()->toDateTimeString();
        $reminders = Reminder::where('status', 'active')
            ->where('remind_at', '<=', $now)
            ->get();

        $count = 0;
        foreach ($reminders as $reminder) {
            $task = $reminder->task;
            $user = $reminder->user;

            if ($task && $user) {
                Mail::send(new TaskReminderNotification(Data::make([
                    'email' => $user->email,
                    'name' => $user->name,
                    'task_title' => $task->title,
                    'task_refid' => $task->refid,
                    'project_name' => $task->project->name,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date ? $task->due_date->format('M d, Y \a\t h:i A') : 'N/A',
                    'task_url' => url($this->config->get('flow.urls.task', 'flow/tasks') . '/' . $task->refid),
                ])));

                $reminder->status = 'sent';
                $reminder->save();
                $count++;
            }
        }

        return $count;
    }
}
