<?php

declare(strict_types=1);

namespace App\Views\Models;

use Database\Pagination\Paginator;

readonly class NotificationLogViewModel
{
    private Paginator $notifications;

    public function __construct(Paginator $notifications)
    {
        $this->notifications = $notifications;
    }

    public function getPageTitle(): string
    {
        return 'Notifications';
    }

    public function getHeading(): string
    {
        return 'Notifications';
    }

    public function getNotifications(): Paginator
    {
        return $this->notifications;
    }

    public function getNotificationsItems(): array
    {
        return $this->notifications->items();
    }

    public function hasNotifications(): bool
    {
        return $this->notifications->exists();
    }

    public function getNoResultComponentData(): array
    {
        return [
            'heading' => 'No Notifications Yet',
            'subheading' => 'You will see important updates and alerts here as they happen.',
            'icon' => 'fas fa-bell',
        ];
    }
}
