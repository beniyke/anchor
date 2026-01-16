<?php

declare(strict_types=1);

return [
    'logo' => [
        'default' => 'img/logo.png',
    ],
    'icon' => 'img/favicon.ico',
    'name' => 'anchorv2',
    'assets' => [
        'photo' => 'public/assets/photo/',
    ],
    'menu' => [
        [
            'type' => ['user', 'admin', 'super-admin', 'senior-admin'],
            'icon' => 'fas fa-home',
            'title' => 'Home',
            'url' => 'account/home',
            'submenu' => false,
            'routes' => false,
        ],
        [
            'type' => ['admin', 'super-admin', 'senior-admin'],
            'icon' => 'fas fa-user',
            'title' => 'Account',
            'url' => '#account',
            'submenu' => [
                [
                    'type' => ['admin', 'super-admin', 'senior-admin'],
                    'icon' => 'fas fa-users',
                    'title' => 'Users',
                    'url' => 'account/user',
                    'routes' => false,
                ],
                [
                    'type' => ['super-admin', 'senior-admin'],
                    'title' => 'Roles',
                    'url' => 'account/role',
                    'routes' => false,
                ],
            ],
            'routes' => false,
        ],
    ]
];
