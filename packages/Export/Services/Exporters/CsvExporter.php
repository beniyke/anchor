<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CSV exporter service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Services\Exporters;

use Core\Services\ConfigServiceInterface;
use Export\Contracts\Exportable;
use Export\Models\ExportHistory;
use Helpers\File\Storage\Storage;

class CsvExporter
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    /**
     * Export data to a file.
     */
    public function export(Exportable $exporter, ExportHistory $history): array
    {
        $path = $this->config->get('export.path', 'exports');
        $filename = $history->filename;
        $relativePath = $path . '/' . $filename; // Storage relative path

        $tempFile = tempnam(sys_get_temp_dir(), 'export_csv_');
        $handle = fopen($tempFile, 'w');

        // Write BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");

        // Write headers
        $headers = $exporter->headers();
        fputcsv($handle, $headers, $this->getDelimiter(), $this->getEnclosure());

        // Write data
        $query = $exporter->query();
        $rowCount = 0;
        $chunkSize = $this->config->get('export.chunk_size', 1000);

        // Handle both collection and query builder
        if (is_object($query) && method_exists($query, 'chunk')) {
            $query->chunk($chunkSize, function ($rows) use ($handle, $exporter, &$rowCount) {
                foreach ($rows as $row) {
                    $mapped = $exporter->map($row);
                    fputcsv($handle, $mapped, $this->getDelimiter(), $this->getEnclosure());
                    $rowCount++;
                }
            });
        } else {
            foreach ($query as $row) {
                $mapped = $exporter->map($row);
                fputcsv($handle, $mapped, $this->getDelimiter(), $this->getEnclosure());
                $rowCount++;
            }
        }

        fclose($handle);

        $stream = fopen($tempFile, 'r');
        Storage::writeStream($relativePath, $stream);
        $size = filesize($tempFile);
        if (is_resource($stream)) {
            fclose($stream);
        }
        @unlink($tempFile);

        return [
            'path' => $relativePath,
            'rows_count' => $rowCount,
            'file_size' => $size,
        ];
    }

    /**
     * Export to string (for direct download).
     */
    public function exportToString(Exportable $exporter): string
    {
        $handle = fopen('php://temp', 'r+');

        // Write BOM for UTF-8
        fwrite($handle, "\xEF\xBB\xBF");

        // Write headers
        $headers = $exporter->headers();
        fputcsv($handle, $headers, $this->getDelimiter(), $this->getEnclosure());

        // Write data
        $query = $exporter->query();

        if (is_object($query) && method_exists($query, 'get')) {
            $query = $query->get();
        }

        foreach ($query as $row) {
            $mapped = $exporter->map($row);
            fputcsv($handle, $mapped, $this->getDelimiter(), $this->getEnclosure());
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    private function getDelimiter(): string
    {
        return $this->config->get('export.formats.csv.delimiter', ',');
    }

    private function getEnclosure(): string
    {
        return $this->config->get('export.formats.csv.enclosure', '"');
    }
}
