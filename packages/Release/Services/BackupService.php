<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Backup Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Services;

use Helpers\File\FileSystem;
use Helpers\File\Paths;
use Helpers\File\Zipper\Zipper;

class BackupService
{
    /**
     * Create a backup of the current application state.
     *
     * @param string $version Current version for naming
     */
    /**
     * Create a backup of the current application state.
     *
     * @param string $version Current version for naming
     */
    public static function create(string $version): ?string
    {
        $backupDir = Paths::storagePath('backups');

        if (!FileSystem::isDir($backupDir)) {
            FileSystem::mkdir($backupDir, 0755, true);
        }

        $filename = "backup-{$version}-" . date('Y-m-d-H-i-s') . '.zip';
        $path = $backupDir . '/' . $filename;

        // Define paths to backup (using relative paths from root for clarity in zip)
        $pathsToBackup = [
            Paths::basePath('App'),
            Paths::basePath('System'),
            Paths::basePath('public'),
            Paths::basePath('packages'),
            Paths::basePath('worker'),
            Paths::basePath('dock'),
            Paths::basePath('composer.json'),
            Paths::basePath('composer.lock'),
        ];

        // Filter valid paths
        $validPaths = array_filter($pathsToBackup, fn ($p) => FileSystem::exists($p));

        if (empty($validPaths)) {
            return null;
        }

        $zipper = new Zipper();
        if ($zipper->zipData($validPaths, $path)) {
            return $path;
        }

        return null;
    }

    public static function restore(string $path): bool
    {
        if (!FileSystem::exists($path)) {
            return false;
        }

        $zipper = new Zipper();
        $result = $zipper->file($path)->path(Paths::basePath())->extract();

        return $result->isSuccess();
    }
}
