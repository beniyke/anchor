<?php

declare(strict_types=1);

return [
    App\Providers\CacheServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Auth\Providers\AuthServiceProvider::class,
    App\Providers\EncryptionServiceProvider::class,
    App\Providers\NotificationServiceProvider::class,
    App\Providers\EventServiceProvider::class,
];
