<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Account\Services\RoleService;
use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Helpers\Http\Request;

readonly class CreateRoleViewModel
{
    private Request $request;

    private ConfigServiceInterface $config;

    private RoleService $service;

    private array $permissions;

    public function __construct(ContainerInterface $container, RoleService $service)
    {
        $this->request = $container->get(Request::class);
        $this->config = $container->get(ConfigServiceInterface::class);
        $this->service = $service;
    }

    public function getPageTitle(): string
    {
        return 'Create Role';
    }

    public function getHeading(): string
    {
        return 'Create Role';
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute();
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('store');
    }

    public function getPermissionRegistry(): array
    {
        return $this->config->get('permissions');
    }

    public function isPermissionChecked(string $slug): bool
    {
        $oldInput = (array) $this->request->old('permission', []);

        return in_array($slug, $oldInput);
    }
}
