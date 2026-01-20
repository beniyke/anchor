<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccessLevel;
use App\Enums\RoleType;
use App\Models\Role;
use App\Requests\RoleRequest;
use App\Views\Models\RoleViewModel;
use Database\Pagination\Paginator;
use Helpers\String\Str;

class RoleService
{
    private const DEFAULT_PER_PAGE = 10;

    public function listRoles(int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $roles = Role::with('user')
            ->paginate($perPage, $page);

        $roles->setItems(RoleViewModel::collection($roles->items()));

        return $roles;
    }

    public function createRole(RoleRequest $request): ?Role
    {
        if (! $request->isValid()) {
            return null;
        }

        $payload = $request->getData();
        $payload->add(['refid' => Str::random('secure')]);
        $role_created = Role::create($payload->data());

        if (! $role_created) {
            return null;
        }

        defer(function () use ($role_created) {
            activity('created {role} role', ['role' => $role_created->title]);
        });

        return $role_created;
    }

    public function updateRole(Role $role, RoleRequest $request): ?Role
    {
        if (! $request->isValid()) {
            return null;
        }

        if ($role->type->isSuperAdmin()) {
            return $role;
        }

        $payload = $request->getData();

        if (! $role->update($payload->data())) {
            return null;
        }

        defer(function () use ($role) {
            activity('updated {role} role', ['role' => $role->title]);
        });

        return $role;
    }

    public function deleteRole(Role $role): bool
    {
        if ($role->type->isSuperAdmin()) {
            return false;
        }

        $role_deleted = $role->title;
        $delete = $role->delete();

        defer(function () use ($role_deleted) {
            activity('deleted role: {role}', ['role' => $role_deleted]);
        });

        return $delete;
    }

    public function getRole(?string $refid = null): ?Role
    {
        if (empty($refid)) {
            return null;
        }

        return Role::findByRefid($refid);
    }

    public function getRoleAccess(): array
    {
        $accessLevels = [];

        foreach (AccessLevel::cases() as $accessLevel) {
            $accessLevels[$accessLevel->value] = $accessLevel->label();
        }

        return $accessLevels;
    }

    public function getRoleTypes(): array
    {
        $roleTypes = [];

        foreach (RoleType::cases() as $roleType) {
            if ($roleType->isSuperAdmin()) {
                continue;
            }

            $roleTypes[$roleType->value] = $roleType->label();
        }

        return $roleTypes;
    }
}
