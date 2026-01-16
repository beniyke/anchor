<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task Assigned Notification
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Notifications;

use Mail\Core\EmailComponent;
use Mail\EmailNotification;

class TaskAssignedNotification extends EmailNotification
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
        return "New Task Assigned: {$this->payload->get('task_title')}";
    }

    public function getTitle(): string
    {
        return 'Task Assigned';
    }

    protected function getRawMessageContent(): string
    {
        $taskUrl = $this->payload->get('task_url');

        return EmailComponent::make()
            ->greeting("Hello {$this->payload->get('name')},")
            ->line("You have been assigned to a new task in project: **{$this->payload->get('project_name')}**")
            ->panel($this->payload->get('task_title'))
            ->attributes([
                'Priority' => ucfirst($this->payload->get('priority')),
                'Due Date' => $this->payload->get('due_date') ?? 'No deadline',
            ])
            ->action('View Task', $taskUrl)
            ->line('Keep up the good work!')
            ->render();
    }
}
