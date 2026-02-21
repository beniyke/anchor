<?php

declare(strict_types=1);

namespace App\Account\Services;

use App\Account\Requests\RoleRequest;
use Database\Pagination\Paginator;
use Helpers\String\Str;
use Permit\Models\Role;
use Permit\Permit;

class RoleService
{
    private const DEFAULT_PER_PAGE = 10;

    public function listRoles(int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $roles = Role::paginate($perPage, $page);

        return $roles;
    }

    public function createRole(RoleRequest $request): ?Role
    {
        if (! $request->isValid()) {
            return null;
        }

        $payload = $request->getData();
        $name = $payload->get('name');
        $description = $payload->get('description');
        $permissions = $payload->get('permission', []);

        $role = Permit::role()
            ->slug(Str::slug($name))
            ->name($name)
            ->description($description)
            ->permissions($permissions)
            ->create();

        activity('created {role} role using Permit', ['role' => $name]);

        return $role;
    }

    public function updateRole(Role $role, RoleRequest $request): ?Role
    {
        if (! $request->isValid()) {
            return null;
        }

        $payload = $request->getData();
        $name = $payload->get('name');
        $description = $payload->get('description');
        $permissions = $payload->get('permission', []);

        $updatedRole = Permit::role()
            ->id($role->id)
            ->slug(Str::slug($name))
            ->name($name)
            ->description($description)
            ->permissions($permissions)
            ->update();

        activity('updated {role} role using Permit', ['role' => $name]);

        return $updatedRole;
    }

    public function deleteRole(Role $role): bool
    {
        if ($role->users()->exists()) {
            return false;
        }

        $roleName = $role->name;

        $delete = $role->delete();

        activity('deleted role: {role} from Permit', ['role' => $roleName]);

        return $delete;
    }

    public function getRole(?string $slug = null): ?Role
    {
        if (empty($slug)) {
            return null;
        }

        return Role::findBySlug($slug);
    }
}
