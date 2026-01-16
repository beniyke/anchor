<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Validation helper for Import package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Services;

use Core\Services\ConfigServiceInterface;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use InvalidArgumentException;

class ImportValidatorService
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly FileMetaInterface $fileMeta
    ) {
    }

    /**
     * Validate an upload file for import.
     *
     * @throws InvalidArgumentException
     */
    public function validateFile(string $filePath, ?string $originalName = null): void
    {
        if (!$this->fileMeta->exists($filePath)) {
            throw new InvalidArgumentException('Import file not found.');
        }

        // Check file size
        $maxSize = $this->config->get('import.max_file_size', 10485760);
        $fileSize = $this->fileMeta->size($filePath);

        if ($fileSize > $maxSize) {
            throw new InvalidArgumentException(
                sprintf(
                    'File size (%s) exceeds maximum allowed (%s).',
                    $this->formatBytes($fileSize),
                    $this->formatBytes($maxSize)
                )
            );
        }

        // Check extension
        $extension = strtolower(pathinfo($originalName ?? $filePath, PATHINFO_EXTENSION));
        $allowedExtensions = $this->config->get('import.allowed_extensions', ['csv']);

        if (!in_array($extension, $allowedExtensions)) {
            throw new InvalidArgumentException(
                sprintf(
                    'File type "%s" is not allowed. Allowed types: %s',
                    $extension,
                    implode(', ', $allowedExtensions)
                )
            );
        }

        if ($fileSize === 0) {
            throw new InvalidArgumentException('Import file is empty.');
        }

        if ($extension === 'csv') {
            $this->validateCsvFile($filePath);
        }
    }

    private function validateCsvFile(string $filePath): void
    {
        $handle = @fopen($filePath, 'r');

        if (!$handle) {
            throw new InvalidArgumentException('Could not read import file.');
        }

        // Check first line (headers)
        $headers = fgetcsv($handle);
        fclose($handle);

        if (!$headers || empty($headers)) {
            throw new InvalidArgumentException('CSV file has no headers or is malformed.');
        }

        // Check for empty headers
        $emptyHeaders = array_filter($headers, fn ($h) => empty(trim($h)));

        if (count($emptyHeaders) > 0) {
            throw new InvalidArgumentException('CSV file contains empty column headers.');
        }
    }

    /**
     * Sanitize a value for safety.
     */
    public static function sanitize(mixed $value): mixed
    {
        if (is_string($value)) {
            // Remove potential formula injection (Excel)
            if (preg_match('/^[=+\-@]/', $value)) {
                $value = "'" . $value;
            }

            // Trim whitespace
            $value = trim($value);

            // Remove null bytes
            $value = str_replace("\0", '', $value);
        }

        return $value;
    }

    /**
     * Sanitize an entire row.
     */
    public static function sanitizeRow(array $row): array
    {
        return array_map([self::class, 'sanitize'], $row);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[(int) $factor]);
    }
}
