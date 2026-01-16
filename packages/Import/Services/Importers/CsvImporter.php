<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CSV importer service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Services\Importers;

use Core\Services\ConfigServiceInterface;
use Exception;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Import\Contracts\Importable;
use Import\Models\ImportError;
use Import\Models\ImportHistory;

class CsvImporter
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly FileMetaInterface $fileMeta
    ) {
    }

    /**
     * Import data from a CSV file.
     */
    public function import(Importable $importer, ImportHistory $history): void
    {
        $filePath = $history->path;

        if (!$this->fileMeta->exists($filePath)) {
            $history->markAsFailed('Import file not found.');

            return;
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            $history->markAsFailed('Could not open import file.');

            return;
        }

        // Read header row
        $headers = fgetcsv($handle);

        if (!$headers) {
            $history->markAsFailed('Could not read file headers.');
            fclose($handle);

            return;
        }

        // Normalize headers
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        // Count total rows
        $totalRows = 0;
        while (fgetcsv($handle) !== false) {
            $totalRows++;
        }

        // Reset to beginning (after header)
        rewind($handle);
        fgetcsv($handle); // Skip header again

        $history->markAsProcessing($totalRows);

        $chunkSize = $this->config->get('import.chunk_size', 500);
        $stopOnError = $this->config->get('import.stop_on_error', false);
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                // Map row to associative array
                $mappedRow = array_combine($headers, $row);

                if ($mappedRow === false) {
                    $this->logError($history, $rowNumber, null, null, 'Column count mismatch', $row);
                    $history->incrementProgress('failed');

                    if ($stopOnError) {
                        break;
                    }

                    continue;
                }

                // Validate using importer rules
                $errors = $this->validateRow($mappedRow, $importer->rules());

                if (!empty($errors)) {
                    foreach ($errors as $column => $error) {
                        $this->logError($history, $rowNumber, $column, $mappedRow[$column] ?? null, $error, $mappedRow);
                    }
                    $history->incrementProgress('failed');

                    if ($stopOnError) {
                        break;
                    }

                    continue;
                }

                // Map and handle
                $data = $importer->map($mappedRow);
                $result = $importer->handle($data);

                if ($result === null) {
                    $history->incrementProgress('skipped');
                } else {
                    $history->incrementProgress('success');
                }
            } catch (Exception $e) {
                $this->logError($history, $rowNumber, null, null, $e->getMessage(), $row);
                $history->incrementProgress('failed');

                if ($stopOnError) {
                    break;
                }
            }
        }

        fclose($handle);
        $history->markAsCompleted();
    }

    /**
     * Validate a row against rules.
     */
    private function validateRow(array $row, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $row[$field] ?? null;

            // Simple validation (can be extended)
            if (str_contains($rule, 'required') && empty($value)) {
                $errors[$field] = "The {$field} field is required.";
            }

            if (str_contains($rule, 'email') && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "The {$field} field must be a valid email.";
            }

            if (str_contains($rule, 'numeric') && !empty($value) && !is_numeric($value)) {
                $errors[$field] = "The {$field} field must be numeric.";
            }
        }

        return $errors;
    }

    /**
     * Log an import error.
     */
    private function logError(
        ImportHistory $history,
        int $rowNumber,
        ?string $column,
        mixed $value,
        string $error,
        mixed $rowData
    ): void {
        ImportError::create([
            'import_id' => $history->id,
            'row_number' => $rowNumber,
            'column' => $column,
            'value' => is_string($value) ? $value : json_encode($value),
            'error' => $error,
            'row_data' => is_array($rowData) ? $rowData : [$rowData],
        ]);
    }
}
