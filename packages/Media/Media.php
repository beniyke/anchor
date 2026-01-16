<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for media operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media;

use Media\Models\Media as MediaModel;
use Media\Services\MediaAnalyticsService;
use Media\Services\MediaManagerService;

class Media
{
    public static function upload(mixed $file, array $options = []): MediaModel
    {
        return resolve(MediaManagerService::class)->upload($file, $options);
    }

    /**
     * Upload from URL.
     */
    public static function uploadFromUrl(string $url, array $options = []): MediaModel
    {
        return resolve(MediaManagerService::class)->uploadFromUrl($url, $options);
    }

    public static function find(int $id): ?MediaModel
    {
        return resolve(MediaManagerService::class)->find($id);
    }

    public static function findByUuid(string $uuid): ?MediaModel
    {
        return resolve(MediaManagerService::class)->findByUuid($uuid);
    }

    public static function url(MediaModel $media, ?string $conversion = null): string
    {
        return resolve(MediaManagerService::class)->getUrl($media, $conversion);
    }

    public static function delete(MediaModel $media): bool
    {
        return resolve(MediaManagerService::class)->delete($media);
    }

    public static function analytics(): MediaAnalyticsService
    {
        return resolve(MediaAnalyticsService::class);
    }

    /**
     * Forward static calls to MediaManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(MediaManagerService::class)->$method(...$arguments);
    }
}
