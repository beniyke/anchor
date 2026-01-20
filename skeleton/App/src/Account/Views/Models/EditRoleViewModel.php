<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Models\Role;
use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;

readonly class EditRoleViewModel
{
    private Role $role;

    private ConfigServiceInterface $config;

    private Request $request;

    private array $permissions;

    public function __construct(ContainerInterface $container, Role $role)
    {
        $this->request = $container->get(Request::class);
        $this->config = $container->get(ConfigServiceInterface::class);
        $this->role = $role;
        $this->permissions = $role->permission;
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

    public function getRoleRefId(): string
    {
        return $this->role->refid;
    }

    public function getRoleTitle(): string
    {
        return $this->role->title;
    }

    public function getTypeValue(): string
    {
        return $this->role->type->value;
    }

    public function getCurrentTypeLabel(): string
    {
        return $this->role->type->label();
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('update/'.$this->getRoleRefId());
    }

    public function getMenuConfig(): array
    {
        return $this->config->get('app.menu');
    }

    public function isMenuAccessible(array $menu): bool
    {
        $type = $this->getTypeValue();

        return in_array($type, $menu['type'] ?? []);
    }

    public function isSubmenuAccessible(array $submenu): bool
    {
        $type = $this->getTypeValue();

        return in_array($type, $submenu['type'] ?? []);
    }

    public function isMenuChecked(string $url): bool
    {
        $key = str_replace('/', '-', $url);

        return in_array($key, $this->permissions['menu'] ?? []);
    }

    public function isSubmenuChecked(string $menuUrl, string $submenuUrl): bool
    {
        $key = str_replace('/', '-', $menuUrl.'::'.$submenuUrl);

        return in_array($key, $this->permissions['submenu'] ?? []);
    }

    public function getMenuId(string $url): string
    {
        return 'mnu-'.str_replace(['/', '#'], ['-', ''], $url);
    }

    public function getSubmenuClass(string $url): string
    {
        return 'sub-'.str_replace(['/', '#'], ['-', ''], $url);
    }

    public function getSubmenuId(string $url): string
    {
        return 'sbm-'.str_replace('/', '-', $url);
    }
}
