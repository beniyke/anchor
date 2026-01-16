<?php

declare(strict_types=1);

return [
    'sender' => ['email' => env('MAIL_SENDER'), 'name' => env('APP_NAME')],
    'paths' => [
        'debug' => 'public/sent-mail',
        'template' => 'App/storage/email-templates',
    ],
    'builder' => [
        'brand' => [
            'name' => env('APP_NAME'),
            'logo' => 'img/logo.png',
        ],
        'templates' => [
            'default' => 'singlepage.html',
            'campaign' => 'campaign.html',
        ],
    ],
    'smtp' => [
        'status' => env('MAIL_SMTP'),
        'debug' => 2,
        'debug_output' => 'error_log',
        'auth' => true,
        /*
        SMTP Server: Main and backup 'smtp1.example.com;smtp2.example.com'
         */
        'host' => env('MAIL_HOST'),
        /*
        SMTP Credentials
         */
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        /*
        Encryption TLS encryption, ssl also accepted
         */
        'encryption' => env('MAIL_ENCRYPTION'),
        /*
        TCP Port to connect to
         */
        'port' => env('MAIL_PORT'),
    ],
];
