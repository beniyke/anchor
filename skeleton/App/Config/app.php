<?php

declare(strict_types=1);

return [
    'logo' => [
        'default' => 'img/logo.png',
    ],
    'icon' => 'img/favicon.ico',
    'name' => 'Anchor',
    'assets' => [
        'photo' => 'public/assets/photo/',
    ],
    'menu' => [
        [
            'icon' => 'fas fa-home',
            'title' => 'Home',
            'url' => 'account/home',
            'permission' => 'home.section',
            'submenu' => false,
            'routes' => false,
        ],
        [
            'icon' => 'fas fa-user',
            'title' => 'Account',
            'url' => '#account',
            'permission' => 'account.section',
            'submenu' => [
                [
                    'icon' => 'fas fa-users',
                    'title' => 'Users',
                    'url' => 'account/user',
                    'permission' => 'users.manage',
                    'routes' => ['account/permission'],
                ],
                [
                    'title' => 'Roles',
                    'url' => 'account/role',
                    'permission' => 'roles.manage',
                    'routes' => false,
                ],
            ],
            'routes' => false,
        ],
    ]
];
