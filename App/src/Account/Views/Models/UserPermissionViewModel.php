<?php

declare(strict_types=1);

namespace App\Account\Views\Models;

use App\Views\Models\UserViewModel;
use Core\Ioc\ContainerInterface;
use Helpers\Http\Request;
use Permit\Models\Permission;
use Permit\Models\UserPermission;

readonly class UserPermissionViewModel
{
    private UserViewModel $user;

    private Request $request;

    private array $groupedPermissions;

    private array $directPermissions;

    private array $inheritedPermissions;

    public function __construct(ContainerInterface $container, UserViewModel $user)
    {
        $this->request = $container->get(Request::class);
        $this->user = $user;

        $this->groupedPermissions = Permission::grouped();
        $this->directPermissions = $this->loadDirectPermissions();
        $this->inheritedPermissions = $this->loadInheritedPermissions();
    }

    private function loadDirectPermissions(): array
    {
        $overrides = UserPermission::where('user_id', $this->user->getId())->get();
        $mapped = [];
        foreach ($overrides as $override) {
            $mapped[$override->permission_id] = $override->type->value;
        }

        return $mapped;
    }

    private function loadInheritedPermissions(): array
    {
        $permissions = [];
        $roles = $this->user->getRoles();
        foreach ($roles as $role) {
            foreach ($role->allPermissions() as $permission) {
                $permissions[$permission->id] = true;
            }
        }

        return $permissions;
    }

    public function getUser(): UserViewModel
    {
        return $this->user;
    }

    public function getGroupedPermissions(): array
    {
        return $this->groupedPermissions;
    }

    public function getPermissionState(Permission $permission): string
    {
        if (isset($this->directPermissions[$permission->id])) {
            return $this->directPermissions[$permission->id];
        }

        return 'inherit';
    }

    public function isInherited(Permission $permission): bool
    {
        return isset($this->inheritedPermissions[$permission->id]);
    }

    public function getPageTitle(): string
    {
        return "Manage Permissions: " . $this->user->getName();
    }

    public function getHeading(): string
    {
        return "Permissions";
    }

    public function getBackUrl(): string
    {
        return $this->request->fullRoute('user', true);
    }

    public function getFormActionUrl(): string
    {
        return $this->request->fullRoute('permission/update/' . $this->user->getRefid(), true);
    }
}
