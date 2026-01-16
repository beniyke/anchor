<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Security helper for Media package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Services;

use Core\Services\ConfigServiceInterface;
use InvalidArgumentException;

class MediaValidatorService
{
    /**
     * Dangerous file extensions.
     */
    private const DANGEROUS_EXTENSIONS = [
        'php',
        'phtml',
        'php3',
        'php4',
        'php5',
        'php7',
        'phar',
        'exe',
        'sh',
        'bat',
        'cmd',
        'com',
        'msi',
        'js',
        'vbs',
        'wsf',
        'asp',
        'aspx',
        'jsp',
        'htaccess',
        'htpasswd',
    ];

    /**
     * Safe image MIME types.
     */
    private const SAFE_IMAGE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    /**
     * Validate a file for upload.
     *
     * @throws InvalidArgumentException
     */
    public function validate(string $filePath, string $originalName, ?string $mimeType = null): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException('File not found.');
        }

        // Check file size
        $maxSize = $this->config->get('media.max_file_size', 10485760);
        $fileSize = filesize($filePath);

        if ($fileSize > $maxSize) {
            throw new InvalidArgumentException('File size exceeds maximum allowed.');
        }

        // Check extension is not dangerous
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (in_array($extension, self::DANGEROUS_EXTENSIONS)) {
            throw new InvalidArgumentException('File type not allowed for security reasons.');
        }

        // Detect actual MIME type (don't trust client-provided)
        $detectedMime = mime_content_type($filePath);

        // Validate MIME type matches extension
        if (!$this->validateMimeExtension($detectedMime, $extension)) {
            throw new InvalidArgumentException('File type does not match extension.');
        }

        // Check allowed MIME types
        $allowedTypes = $this->config->get('media.allowed_types', []);

        if (!empty($allowedTypes) && !in_array($detectedMime, $allowedTypes)) {
            throw new InvalidArgumentException('File MIME type not allowed.');
        }

        // For images, validate they are actual images
        if (str_starts_with($detectedMime, 'image/') && !$this->isValidImage($filePath)) {
            throw new InvalidArgumentException('Invalid or corrupted image file.');
        }

        if ($this->containsPhpCode($filePath)) {
            throw new InvalidArgumentException('File contains potentially malicious code.');
        }
    }

    private function validateMimeExtension(string $mimeType, string $extension): bool
    {
        $mimeToExtension = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'image/svg+xml' => ['svg'],
            'application/pdf' => ['pdf'],
            'video/mp4' => ['mp4'],
            'video/webm' => ['webm'],
            'audio/mpeg' => ['mp3'],
            'audio/wav' => ['wav'],
        ];

        if (isset($mimeToExtension[$mimeType])) {
            return in_array($extension, $mimeToExtension[$mimeType]);
        }

        return true; // Allow unknown types if not in blocked list
    }

    private function isValidImage(string $filePath): bool
    {
        $imageInfo = @getimagesize($filePath);

        return $imageInfo !== false && $imageInfo[0] > 0 && $imageInfo[1] > 0;
    }

    private function containsPhpCode(string $filePath): bool
    {
        $content = file_get_contents($filePath, false, null, 0, 1024); // Check first 1KB

        if ($content === false) {
            return false;
        }

        $patterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?[\s\r\n]/i',
            '/<%=/i',
            '/<script\s+language\s*=\s*["\']?php["\']?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    public static function sanitizeFilename(string $filename): string
    {
        // Remove path traversal
        $filename = basename($filename);

        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Limit length
        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 190) . '.' . $extension;
        }

        return $filename;
    }
}
