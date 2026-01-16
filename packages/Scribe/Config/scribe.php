<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * scribe.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Scribe Default Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define settings for the Scribe blogging package.
    |
    */

    'posts' => [
        'per_page' => 10,
        'auto_slug' => true,
    ],

    'comments' => [
        'enabled' => true,
        'require_approval' => false,
    ],
];
