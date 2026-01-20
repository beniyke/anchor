<?php

declare(strict_types=1);

return [
    'path' => env('CACHE_PATH', 'cache'),
    'prefix' => env('CACHE_PREFIX', ''),
    'extension' => env('CACHE_EXTENSION', 'cache'),
    'query' => [
        'enabled' => env('QUERY_CACHE_ENABLED', true),
        'default_ttl' => env('QUERY_CACHE_TTL', 3600),
        'max_items' => env('QUERY_CACHE_MAX_ITEMS', 10000),
        'use_stale' => env('QUERY_CACHE_USE_STALE', true),
        'auto_invalidate' => env('QUERY_CACHE_AUTO_INVALIDATE', true),
        'jitter_percent' => 0.1,
    ],
    /**
     * Classes allowed during cache unserialization
     * Set to true to allow all classes, or provide an array of specific class names
     * Setting to false or [] will cause __PHP_Incomplete_Class errors
     */
    'allowed_classes' => true,
];
