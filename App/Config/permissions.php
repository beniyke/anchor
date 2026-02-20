<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * System Permission Registry
 *
 * This file contains all available privileges. Your Admin UI (Role/User edit pages)
 * should loop through this to build the checkbox matrix.
 */

return [
    'General' => [
        'home.section' => 'Home',
    ],
    'User Management' => [
        'account.section' => 'Account Menu',
        'users.manage'   => 'Users List',
        'users.create' => 'Create Users',
        'users.edit'   => 'Edit Users',
        'users.delete' => 'Delete Users',
    ],
    'Access Control' => [
        'roles.manage' => 'Roles List',
        'roles.create' => 'Create Roles',
        'roles.edit'   => 'Edit Roles',
        'roles.delete' => 'Delete Roles',
    ],
];
