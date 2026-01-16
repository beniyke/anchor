<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Version Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Services;

use Core\App;
use Helpers\File\FileSystem;
use Helpers\File\Paths;

class VersionService
{
    public static function current(): string
    {
        $path = Paths::basePath('version.txt');

        if (FileSystem::exists($path)) {
            return trim(FileSystem::get($path));
        }

        return App::VERSION;
    }

    public static function update(string $version): bool
    {
        return FileSystem::put(Paths::basePath('version.txt'), $version);
    }
}
