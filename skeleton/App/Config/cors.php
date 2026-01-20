<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | CORS (Cross-Origin Resource Sharing) Configuration
    |--------------------------------------------------------------------------
    |
    | Configure CORS settings for API endpoints. CORS is disabled by default.
    | Enable it only if you're building APIs that need cross-origin access.
    |
    */

    'enabled' => env('CORS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Specify which origins are allowed to access your API.
    | Use '*' to allow all origins (not recommended for production).
    | Use '*.example.com' for wildcard subdomain matching.
    |
    */

    'allowed_origins' => [
        // '*', // Allow all origins (development only)
        // 'https://example.com',
        // 'https://*.example.com', // Wildcard subdomain
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed HTTP Methods
    |--------------------------------------------------------------------------
    |
    | Specify which HTTP methods are allowed for CORS requests.
    |
    */

    'allowed_methods' => [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Specify which headers are allowed in CORS requests.
    |
    */

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Requested-With',
        'X-CSRF-Token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are safe to expose to the client.
    |
    */

    'exposed_headers' => [
        'X-Total-Count',
        'X-Page-Count',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allow Credentials
    |--------------------------------------------------------------------------
    |
    | Whether to allow credentials (cookies, authorization headers) in
    | cross-origin requests. Cannot be used with 'allowed_origins' => ['*'].
    |
    */

    'allow_credentials' => false,

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) the results of a preflight request can be cached.
    |
    */

    'max_age' => 86400, // 24 hours
];
