<?php

declare(strict_types=1);

namespace App\Account\Actions;

use App\Account\Requests\PermissionRequest;
use App\Models\User;

class UpdateUserPermissionAction
{
    public function execute(User $user, PermissionRequest $request): bool
    {
        if (!$request->isValid()) {
            return false;
        }

        $permissions = $request->permissions;

        $grants = [];
        $denies = [];

        foreach ($permissions as $slug => $value) {
            if ($value === 'grant') {
                $grants[] = (string) $slug;
            } elseif ($value === 'deny') {
                $denies[] = (string) $slug;
            }
        }

        $user->syncPermissions($grants, $denies);

        activity('updated permission overrides for {user}', ['user' => $user->name]);

        return true;
    }
}
