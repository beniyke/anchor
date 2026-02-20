<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Flash;
use Helpers\Http\Request;

readonly class EditRoleViewModel
{
    private RoleViewModel $role;

    private ConfigServiceInterface $config;

    private Request $request;

    private Flash $flash;

    public function __construct(ContainerInterface $container, RoleViewModel $role)
    {
        $this->request = $container->get(Request::class);
        $this->flash = $container->get(Flash::class);
        $this->config = $container->get(ConfigServiceInterface::class);
        $this->role = $role;
    }

    public function getPageTitle(): string
    {
        return 'Edit Role';
    }

    public function getHeading(): string
    {
        return 'Edit Role';
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute();
    }

    public function getRoleSlug(): string
    {
        return $this->role->getSlug();
    }

    public function getRoleName(): string
    {
        return $this->role->getName();
    }

    public function getRoleDescription(): string
    {
        return $this->role->getDescription();
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update/' . $this->getRoleSlug());
    }

    public function getPermissionRegistry(): array
    {
        return $this->config->get('permissions');
    }

    public function isPermissionChecked(string $slug): bool
    {
        $oldInput = (array) $this->flash->old('permission', []);

        if (! empty($oldInput)) {
            return in_array($slug, $oldInput);
        }

        return $this->role->hasPermission($slug);
    }
}
