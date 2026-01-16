<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core media manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Services;

use Core\Services\ConfigServiceInterface;
use Helpers\File\Adapters\Interfaces\FileManipulationInterface;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Adapters\Interfaces\FileReadWriteInterface;
use Helpers\File\Adapters\Interfaces\PathResolverInterface;
use Helpers\String\Str;
use InvalidArgumentException;
use Media\Models\Media;
use RuntimeException;

class MediaManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly PathResolverInterface $paths,
        private readonly FileMetaInterface $fileMeta,
        private readonly FileReadWriteInterface $fileReadWrite,
        private readonly FileManipulationInterface $fileManipulation
    ) {
    }

    public function upload(mixed $file, array $options = []): Media
    {
        // Handle uploaded file array or path string
        if (is_array($file)) {
            $tmpPath = $file['tmp_name'];
            $originalName = $file['name'];
            $mimeType = $file['type'];
            $size = $file['size'];
        } elseif (is_string($file)) {
            $tmpPath = $file;
            $originalName = $options['filename'] ?? basename($file);
            $mimeType = $this->getMimeType($file);
            $size = $this->fileMeta->size($file);
        } else {
            throw new InvalidArgumentException('Invalid file input.');
        }

        // Validate
        $this->validateFile($mimeType, $size);

        // Generate unique filename
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = Str::random('secure') . '.' . $extension;

        // Determine storage path
        $basePath = $this->getBasePath();
        $datePath = date('Y/m');
        $storagePath = $basePath . DIRECTORY_SEPARATOR . $datePath;

        // Ensure directory exists
        if (!$this->fileMeta->isDir($storagePath)) {
            $this->fileManipulation->mkdir($storagePath, 0755, true);
        }

        $fullPath = $storagePath . DIRECTORY_SEPARATOR . $filename;

        // Move/copy file
        if (is_array($file)) {
            move_uploaded_file($tmpPath, $fullPath);
        } else {
            $this->fileManipulation->copy($tmpPath, $fullPath);
        }

        $media = Media::create([
            'uuid' => Str::random('secure'),
            'disk' => $this->config->get('media.disk', 'local'),
            'path' => $datePath,
            'filename' => $filename,
            'original_filename' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'collection' => $options['collection'] ?? 'default',
            'conversions' => [],
            'metadata' => $options['metadata'] ?? [],
        ]);

        // Generate conversions for images
        if ($media->isImage() && $this->config->get('media.auto_conversions', true)) {
            $this->generateConversions($media);
        }

        return $media;
    }

    public function uploadFromUrl(string $url, array $options = []): Media
    {
        $content = file_get_contents($url);

        if ($content === false) {
            throw new RuntimeException('Could not download file from URL.');
        }

        // Save to temp file
        $tmpFile = tempnam(sys_get_temp_dir(), 'media_');
        file_put_contents($tmpFile, $content);

        $options['filename'] = $options['filename'] ?? basename(parse_url($url, PHP_URL_PATH));

        try {
            $media = $this->upload($tmpFile, $options);
        } finally {
            @unlink($tmpFile);
        }

        return $media;
    }

    public function find(int $id): ?Media
    {
        return Media::find($id);
    }

    public function findByUuid(string $uuid): ?Media
    {
        return Media::findByUuid($uuid);
    }

    public function getUrl(Media $media, ?string $conversion = null): string
    {
        $path = $conversion && $media->hasConversion($conversion)
            ? $media->getConversionPath($conversion)
            : $media->getFullPath();

        $basePath = $this->config->get('media.path', 'media');

        return '/' . $basePath . '/' . $path;
    }

    public function getPath(Media $media, ?string $conversion = null): string
    {
        $path = $conversion && $media->hasConversion($conversion)
            ? $media->getConversionPath($conversion)
            : $media->getFullPath();

        return $this->getBasePath() . DIRECTORY_SEPARATOR . $path;
    }

    public function delete(Media $media): bool
    {
        // Delete conversions
        foreach ($media->conversions ?? [] as $conversion) {
            $conversionPath = $this->getBasePath() . DIRECTORY_SEPARATOR . $conversion['path'];

            if ($this->fileMeta->exists($conversionPath)) {
                $this->fileManipulation->delete($conversionPath);
            }
        }

        // Delete original
        $originalPath = $this->getPath($media);

        if ($this->fileMeta->exists($originalPath)) {
            $this->fileManipulation->delete($originalPath);
        }

        return $media->delete();
    }

    public function generateConversions(Media $media): void
    {
        if (!$media->isImage()) {
            return;
        }

        $conversions = $this->config->get('media.conversions', []);
        $generatedConversions = [];

        foreach ($conversions as $name => $settings) {
            $conversionFilename = pathinfo($media->filename, PATHINFO_FILENAME) . "_{$name}." . pathinfo($media->filename, PATHINFO_EXTENSION);
            $conversionPath = $media->path . DIRECTORY_SEPARATOR . $conversionFilename;
            $fullPath = $this->getBasePath() . DIRECTORY_SEPARATOR . $conversionPath;

            // Simple image resize using GD
            if ($this->resizeImage(
                $this->getPath($media),
                $fullPath,
                $settings['width'] ?? null,
                $settings['height'] ?? null
            )) {
                $generatedConversions[$name] = [
                    'path' => $conversionPath,
                    'width' => $settings['width'],
                    'height' => $settings['height'],
                ];
            }
        }

        $media->update(['conversions' => $generatedConversions]);
    }

    private function resizeImage(string $source, string $destination, ?int $width, ?int $height): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $info = getimagesize($source);

        if (!$info) {
            return false;
        }

        $sourceImage = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($source),
            'image/png' => imagecreatefrompng($source),
            'image/gif' => imagecreatefromgif($source),
            'image/webp' => imagecreatefromwebp($source),
            default => null,
        };

        if (!$sourceImage) {
            return false;
        }

        $srcWidth = imagesx($sourceImage);
        $srcHeight = imagesy($sourceImage);

        if ($width && !$height) {
            $height = (int) ($srcHeight * ($width / $srcWidth));
        } elseif ($height && !$width) {
            $width = (int) ($srcWidth * ($height / $srcHeight));
        } elseif (!$width && !$height) {
            $width = $srcWidth;
            $height = $srcHeight;
        }

        $newImage = imagecreatetruecolor($width, $height);

        // Preserve transparency
        if ($info['mime'] === 'image/png' || $info['mime'] === 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);

        $quality = $this->config->get('media.quality', 85);

        $result = match ($info['mime']) {
            'image/jpeg' => imagejpeg($newImage, $destination, $quality),
            'image/png' => imagepng($newImage, $destination, (int) (($quality - 1) / 10)),
            'image/gif' => imagegif($newImage, $destination),
            'image/webp' => imagewebp($newImage, $destination, $quality),
            default => false,
        };

        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $result;
    }

    private function validateFile(string $mimeType, int $size): void
    {
        $maxSize = $this->config->get('media.max_file_size', 10485760);
        $allowedTypes = $this->config->get('media.allowed_types', []);

        if ($size > $maxSize) {
            throw new RuntimeException('File size exceeds maximum allowed.');
        }

        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            throw new RuntimeException('File type not allowed.');
        }
    }

    private function getMimeType(string $path): string
    {
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    private function getBasePath(): string
    {
        $mediaPath = $this->config->get('media.path', 'media');

        return $this->paths->storagePath('app') . DIRECTORY_SEPARATOR . $mediaPath;
    }
}
