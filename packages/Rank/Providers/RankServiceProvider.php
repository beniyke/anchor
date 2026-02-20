<?php

declare(strict_types=1);

namespace Rank\Providers;

use Core\Services\ServiceProvider;
use Core\Views\ViewEngine;
use Helpers\File\Paths;
use Helpers\Http\Request;
use Rank\Rank;
use Rank\Services\SeoManager;

class RankServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadHelpers(Paths::packagePath('Rank/Helpers/rank.php'));

        $this->container->singleton(SeoManager::class, function ($container) {
            return new SeoManager($container->has(Request::class) ? $container->get(Request::class) : null);
        });
    }

    public function boot(): void
    {
        $container = $this->container;
        ViewEngine::macro('rank', function () use ($container) {
            return $container->get(Rank::class);
        });
    }
}
