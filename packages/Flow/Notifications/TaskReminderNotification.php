<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task Reminder Notification
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class TaskReminderNotification extends EmailNotification
{
    public function getRecipients(): array
    {
        return [
            'to' => [
                $this->payload->get('email') => $this->payload->get('name')
            ]
        ];
    }

    public function getSubject(): string
    {
        return "Reminder: Task '{$this->payload->get('task_title')}' is due soon";
    }

    public function getTitle(): string
    {
        return 'Task Reminder';
    }

    protected function getRawMessageContent(): string
    {
        $taskUrl = $this->payload->get('task_url');

        return EmailComponent::make()
            ->greeting("Hello {$this->payload->get('name')},")
            ->line("This is a reminder that the task **{$this->payload->get('task_title')}** is approaching its deadline.")
            ->attributes([
                'Project' => $this->payload->get('project_name'),
                'Due Date' => $this->payload->get('due_date'),
                'Priority' => ucfirst($this->payload->get('priority')),
            ])
            ->action('View Task', $taskUrl)
            ->line('Please ensure all required work is completed on time.')
            ->render();
    }
}
