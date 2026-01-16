<?php

declare(strict_types=1);

namespace App\Views\Models;

use Database\Pagination\Paginator;

readonly class ActivityLogViewModel
{
    private readonly Paginator $activities;

    public function __construct(Paginator $activities)
    {
        $this->activities = $activities;
    }

    public function getPageTitle(): string
    {
        return 'Activity';
    }

    public function getHeading(): string
    {
        return 'Activity';
    }

    public function getActivities(): Paginator
    {
        return $this->activities;
    }

    public function getActivitiesItems(): array
    {
        return $this->activities->items();
    }

    public function hasActivities(): bool
    {
        return $this->activities->exists();
    }

    public function getNoResultComponentData(): array
    {
        return [
            'heading' => 'No Activity Found',
            'subheading' => 'Activity logs monitor and provide insights into important actions on your account.',
            'icon' => 'activity',
        ];
    }
}
