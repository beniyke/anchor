<?php

declare(strict_types=1);

return [
    'api/cron' => [
        'type' => 'static',
        'token' => env('CRON_API_KEY'),
    ],
    'api/test' => [
        'type' => 'dynamic',
    ],
    'api/user' => [
        'type' => 'auth',
        'abilities' => ['*'],
    ],
];
