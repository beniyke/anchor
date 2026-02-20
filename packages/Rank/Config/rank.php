<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Meta Tags
    |--------------------------------------------------------------------------
    |
    | These values will be used as defaults if no specific values are provided.
    |
    */

    'defaults' => [
        'title' => 'Anchor Framework',
        'description' => 'A powerful, lightweight PHP framework.',
        'image' => '',
        'type' => 'website',
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Graph Settings
    |--------------------------------------------------------------------------
    */

    'og' => [
        'site_name' => 'Anchor App',
        'type' => 'website',
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter Settings
    |--------------------------------------------------------------------------
    */

    'twitter' => [
        'card' => 'summary_large_image',
        'site' => '@anchorphp',
    ],
];
