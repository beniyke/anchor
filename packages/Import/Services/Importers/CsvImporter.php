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

use Core\Event;
use Core\Services\ConfigServiceInterface;
use Database\DB;
use Exception;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Storage\Storage;
use Import\Contracts\Importable;
use Import\Events\RowProcessed;
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

        if (!Storage::exists($filePath)) {
            $history->markAsFailed('Import file not found.');

            return;
        }

        $handle = Storage::readStream($filePath);

        if (!$handle) {
            $history->markAsFailed('Could not open import file stream.');

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
        $meta = stream_get_meta_data($handle);

        if ($meta['seekable']) {
            while (fgetcsv($handle) !== false) {
                $totalRows++;
            }
            rewind($handle);
            fgetcsv($handle); // Skip header again
        }

        $history->markAsProcessing($totalRows);

        $chunkSize = $this->config->get('import.chunk_size', 500);
        $stopOnError = $this->config->get('import.stop_on_error', false);
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            try {
                // Map row to associative array
                if (count($headers) !== count($row)) {
                    // Handle mismatch
                    continue;
                }

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
                    Event::dispatch(new RowProcessed($history, $rowNumber, $totalRows, 'skipped'));
                } else {
                    $history->incrementProgress('success');
                    Event::dispatch(new RowProcessed($history, $rowNumber, $totalRows, 'success'));
                }
            } catch (Exception $e) {
                $this->logError($history, $rowNumber, null, null, $e->getMessage(), $row);
                $history->incrementProgress('failed');
                Event::dispatch(new RowProcessed($history, $rowNumber, $totalRows, 'failed'));

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

        foreach ($rules as $field => $fieldRules) {
            $value = $row[$field] ?? null;

            // Normalize rules to array
            if (is_string($fieldRules)) {
                $fieldRules = explode('|', $fieldRules);
                $temp = [];
                foreach ($fieldRules as $r) {
                    if ($r === 'required') {
                        $temp['required'] = true;
                    } elseif ($r === 'email') {
                        $temp['type'] = 'email';
                    } elseif ($r === 'numeric') {
                        $temp['type'] = 'numeric';
                    }
                }
                $fieldRules = $temp;
            }

            // Required
            if (($fieldRules['required'] ?? false) && (is_null($value) || $value === '')) {
                $errors[$field] = "The {$field} field is required.";
                continue;
            }

            if (is_null($value) || $value === '') {
                continue;
            }

            // Type
            if (isset($fieldRules['type'])) {
                switch ($fieldRules['type']) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "The {$field} field must be a valid email.";
                        }
                        break;
                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field] = "The {$field} field must be numeric.";
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            $errors[$field] = "The {$field} field must be a string.";
                        }
                        break;
                }
            }

            // Maxlength
            if (isset($fieldRules['maxlength']) && strlen((string)$value) > $fieldRules['maxlength']) {
                $errors[$field] = "The {$field} field must not exceed {$fieldRules['maxlength']} characters.";
            }

            // Unique: table.column
            if (isset($fieldRules['unique'])) {
                [$table, $column] = explode('.', $fieldRules['unique']);
                $exists = DB::connection()->table($table)->where($column, $value)->exists();
                if ($exists) {
                    $errors[$field] = "The {$field} has already been taken.";
                }
            }

            // Exist: table.column
            if (isset($fieldRules['exist'])) {
                [$table, $column] = explode('.', $fieldRules['exist']);
                $exists = DB::connection()->table($table)->where($column, $value)->exists();
                if (!$exists) {
                    $errors[$field] = "The selected {$field} is invalid.";
                }
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
