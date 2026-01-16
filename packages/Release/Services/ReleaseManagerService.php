<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Release Manager
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Release\Services;

use Core\Services\ConfigServiceInterface;
use Exception;
use Helpers\File\FileSystem;
use Helpers\File\Paths;
use Helpers\File\Zipper\Zipper;
use Helpers\Http\Client\Curl;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ReleaseManagerService
{
    protected string $endpoint;

    protected string $tmpFolder;

    protected bool $maintenanceMode;

    protected bool $backupEnabled;

    protected array $authorizedUsers;

    protected array $excluded;

    public function __construct(ConfigServiceInterface $config)
    {
        $this->endpoint = rtrim($config->get('release.endpoint') ?? '', '/');
        $this->tmpFolder = Paths::basePath($config->get('release.tmp_folder') ?? 'App/storage/tmp');
        $this->maintenanceMode = $config->get('release.maintenance_mode') ?? true;
        $this->backupEnabled = $config->get('release.backup') ?? true;
        $this->authorizedUsers = $config->get('release.authorized_users') ?? [];
        $this->excluded = $config->get('release.exclude') ?? [];
    }

    public function analytics(): ReleaseAnalyticsService
    {
        return resolve(ReleaseAnalyticsService::class);
    }

    public function check(): ?array
    {
        try {
            $client = new Curl();
            $response = $client->get($this->endpoint . '/release.json')->send();

            if (!$response->ok()) {
                return null;
            }

            $data = $response->json();

            if (!$data || !isset($data['version'])) {
                return null;
            }

            $currentVersion = VersionService::current();

            if (version_compare($data['version'], $currentVersion, '>')) {
                return $data;
            }

            return null;
        } catch (Exception $e) {
            logger('release.log')->error("Release check failed: " . $e->getMessage());

            return null;
        }
    }

    public function update(): bool
    {
        $update = $this->check();

        if (!$update) {
            return false;
        }

        if ($this->maintenanceMode) {
            EnvUpdaterService::update('FIREWALL_ACTIVATE_MAINTENANCE_GUARD', 'true');
        }

        try {
            $backupPath = null;
            if ($this->backupEnabled) {
                $backupPath = BackupService::create(VersionService::current());
            }

            $archivePath = $this->download($update['archive']);

            if (!$archivePath) {
                throw new Exception("Failed to download update archive.");
            }

            if (!FileSystem::isDir($this->tmpFolder)) {
                FileSystem::mkdir($this->tmpFolder, 0755, true);
            }

            $zipper = new Zipper();
            $result = $zipper->file($archivePath)->path($this->tmpFolder)->extract();

            if (!$result->isSuccess()) {
                throw new Exception("Failed to extract update: " . $result->getMessage());
            }

            $upgradeScript = $this->tmpFolder . '/upgrade.php';
            if (FileSystem::exists($upgradeScript)) {
                require_once $upgradeScript;

                // @phpstan-ignore-next-line
                if (function_exists('beforeUpdate')) {
                    beforeUpdate();
                }
            }

            $this->installFiles($this->tmpFolder);

            if (FileSystem::exists($upgradeScript)) {
                if (function_exists('afterUpdate')) {
                    afterUpdate();
                }
            }

            FileSystem::delete($this->tmpFolder);

            if ($this->maintenanceMode) {
                EnvUpdaterService::update('FIREWALL_ACTIVATE_MAINTENANCE_GUARD', 'false');
            }

            return true;
        } catch (Exception $e) {
            if ($this->maintenanceMode) {
                EnvUpdaterService::update('FIREWALL_ACTIVATE_MAINTENANCE_GUARD', 'false');
            }

            if (isset($backupPath)) {
                BackupService::restore($backupPath);
            }

            throw $e;
        }
    }

    protected function download(string $url): ?string
    {
        $client = new Curl();
        $destination = $this->tmpFolder . '/update.zip';

        if (!FileSystem::isDir($this->tmpFolder)) {
            FileSystem::mkdir($this->tmpFolder, 0755, true);
        }

        $success = $client->download($url, $destination);

        return $success ? $destination : null;
    }

    protected function installFiles(string $source): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $rootPath = Paths::basePath();

        foreach ($iterator as $item) {
            $subPath = substr($item->getPathname(), strlen($source) + 1);
            if ($subPath === 'upgrade.php' || $subPath === 'update.zip') {
                continue;
            }

            $target = $rootPath . '/' . $subPath;

            if ($item->isDir()) {
                if (!FileSystem::isDir($target)) {
                    FileSystem::mkdir($target);
                }
            } else {
                // Check excludes
                foreach ($this->excluded as $exclude) {
                    if (str_starts_with($subPath, $exclude)) {
                        continue 2;
                    }
                }

                FileSystem::copy($item->getPathname(), $target);
            }
        }
    }
}
