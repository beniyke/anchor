<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use Core\Ioc\ContainerInterface;
use Database\Pagination\Paginator;
use Helpers\Http\Request;

readonly class RoleListViewModel
{
    private Paginator $roles;

    private Request $request;

    public function __construct(ContainerInterface $container, Paginator $roles)
    {
        $this->request = $container->get(Request::class);
        $this->roles = $roles;
    }

    public function getPageTitle(): string
    {
        return 'Roles';
    }

    public function getHeading(): string
    {
        return 'Roles';
    }

    public function getCreateActionUrl(): string
    {
        return $this->request->fullRoute('create');
    }

    public function getEditActionUrl(string $refid): string
    {
        return $this->request->fullRoute('edit/'.$refid);
    }

    public function getDeleteActionUrl(string $refid): string
    {
        return $this->request->fullRoute('destroy/'.$refid);
    }

    public function getRoles(): Paginator
    {
        return $this->roles;
    }

    public function getRolesItems(): array
    {
        return $this->roles->items();
    }

    public function hasRoles(): bool
    {
        return $this->roles->exists();
    }

    public function getNoResultComponentData(): array
    {
        return [
            'heading' => 'No Roles Yet',
            'subheading' => 'Roles are yet to be created.',
            'icon' => 'fas fa-tags',
            'cta' => [
                'url' => $this->getCreateActionUrl(),
                'content' => 'Create Role',
            ],
        ];
    }
}
