<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the User model provider.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'source' => 'user',
            'route' => [
                'login' => 'login',
                'home' => 'account/home'
            ],
        ],

        'admin' => [
            'driver' => 'session',
            'source' => 'user', // You can change this to 'admin' if you have a separate table
            'route' => [
                'login' => 'admin/login',
                'home' => 'admin/home'
            ],
        ],

        'api' => [
            'driver' => 'token',
            'source' => 'user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Sources
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user source. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database"
    |
    */

    'sources' => [
        'user' => [
            'driver' => 'database',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Security
    |--------------------------------------------------------------------------
    |
    | Here you may define settings related to password security, such as
    | the maximum age of a password before it must be updated.
    |
    */

    'password_max_age_days' => 90,

];
