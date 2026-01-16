<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Analytics Service
 *
 * Provides insights into system updates and backup health.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Services;

use Helpers\File\FileSystem;
use Helpers\File\Paths;

class ReleaseAnalyticsService
{
    public function getVersionStats(): array
    {
        return [
            'current_version' => VersionService::current(),
            'last_updated' => FileSystem::exists(Paths::basePath('version.txt'))
                ? date('Y-m-d H:i:s', FileSystem::lastModified(Paths::basePath('version.txt')))
                : null,
        ];
    }

    public function getBackupStats(): array
    {
        $backupDir = Paths::storagePath('backups');

        if (!FileSystem::isDir($backupDir)) {
            return [
                'total_backups' => 0,
                'total_size_bytes' => 0,
                'last_backup' => null,
                'backups' => [],
            ];
        }

        $files = glob($backupDir . '/*.zip') ?: [];
        $backups = [];
        $totalSize = 0;
        $lastBackup = null;

        foreach ($files as $file) {
            $size = FileSystem::size($file);
            $modified = FileSystem::lastModified($file);
            $totalSize += $size;

            if ($lastBackup === null || $modified > $lastBackup) {
                $lastBackup = $modified;
            }

            $backups[] = [
                'filename' => basename($file),
                'size' => $size,
                'created_at' => date('Y-m-d H:i:s', $modified),
            ];
        }

        // Sort backups by date DESC
        usort($backups, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return [
            'total_backups' => count($backups),
            'total_size_bytes' => $totalSize,
            'last_backup' => $lastBackup ? date('Y-m-d H:i:s', $lastBackup) : null,
            'backups' => array_slice($backups, 0, 10), // Return last 10
        ];
    }
}
