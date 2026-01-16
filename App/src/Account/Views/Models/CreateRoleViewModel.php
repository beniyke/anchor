<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Services\RoleService;
use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;

readonly class CreateRoleViewModel
{
    private Request $request;

    private ConfigServiceInterface $config;

    private RoleService $service;

    private array $permissions;

    public function __construct(ContainerInterface $container, RoleService $service, array $permissions = ['menu' => [], 'submenu' => []])
    {
        $this->request = $container->get(Request::class);
        $this->config = $container->get(ConfigServiceInterface::class);
        $this->service = $service;
        $this->permissions = $permissions;
    }

    public function getPageTitle(): string
    {
        return 'Create Role';
    }

    public function getHeading(): string
    {
        return 'Create Role';
    }

    public function shouldShowTypeSelection(): bool
    {
        return ! $this->request->filled('type');
    }

    public function getRoleTypes(): array
    {
        $options = ['' => 'SELECT'];

        return array_merge($options, $this->service->getRoleTypes());
    }

    public function getTypeFormActionUrl(): string
    {
        return $this->request->fullRoute('create');
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute($this->request->filled('type') ? 'create' : '');
    }

    public function shouldShowPermissionForm(): bool
    {
        $type = $this->request->get('type');

        return $this->request->filled('type') && array_key_exists($type, $this->service->getRoleTypes());
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('store');
    }

    public function getTypeValue(): string
    {
        return (string) $this->request->get('type');
    }

    public function getCurrentTypeLabel(): string
    {
        $type = $this->getTypeValue();

        return $this->service->getRoleTypes()[$type] ?? 'Unknown Type';
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
