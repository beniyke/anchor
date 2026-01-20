<?php

declare(strict_types=1);

namespace App\Providers;

use Core\Services\ConfigServiceInterface;
use Core\Services\ServiceProvider;
use Helpers\File\Cache;
use Helpers\File\Contracts\CacheInterface;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(CacheInterface::class, function ($container) {
            $config = $container->get(ConfigServiceInterface::class);

            return Cache::create(
                $config->get('cache.path'),
                $config->get('cache.prefix'),
                $config->get('cache.extension')
            );
        });
    }
}
