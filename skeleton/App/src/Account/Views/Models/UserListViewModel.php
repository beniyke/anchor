<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use Core\Ioc\ContainerInterface;
use Database\Pagination\Paginator;
use Helpers\Http\Request;

readonly class UserListViewModel
{
    private Paginator $users;

    private Request $request;

    public function __construct(ContainerInterface $container, Paginator $users)
    {
        $this->request = $container->get(Request::class);
        $this->users = $users;
    }

    public function getPageTitle(): string
    {
        return 'Users';
    }

    public function getHeading(): string
    {
        return 'Users';
    }

    public function getSearchValue(): ?string
    {
        return $this->request->get('search');
    }

    public function isSearching(): bool
    {
        return $this->request->filled('search');
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute();
    }

    public function getCreateActionUrl(): string
    {
        return $this->request->fullRoute('create');
    }

    public function getSearchFormAction(): string
    {
        return $this->request->fullRoute();
    }

    public function getActivityUrl(string $refid): string
    {
        return $this->request->fullRoute('activity/user/'.$refid, true);
    }

    public function getEditUrl(string $refid): string
    {
        return $this->request->fullRoute('edit/'.$refid);
    }

    public function getDeleteUrl(string $refid): string
    {
        return $this->request->fullRoute('destroy/'.$refid);
    }

    public function getResendUrl(string $refid): string
    {
        return $this->request->fullRoute(params: ['resend' => $refid]);
    }

    public function hasUsers(): bool
    {
        return $this->users->exists();
    }

    public function getUsers(): Paginator
    {
        return $this->users;
    }

    public function getUsersItems(): array
    {
        return $this->users->items();
    }

    public function getNoResultComponentData(): array
    {
        $isSearching = $this->isSearching();

        return [
            'heading' => 'No User Found',
            'subheading' => $isSearching ? 'No result found for your search' : 'Users are yet to be created.',
            'icon' => 'fas '.($isSearching ? 'fa-search' : 'fa-users'),
            'cta' => $isSearching ? null : [
                'url' => $this->getCreateActionUrl(),
                'content' => 'Create User',
            ],
        ];
    }
}
