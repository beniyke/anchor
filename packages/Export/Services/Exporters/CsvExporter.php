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
use Helpers\File\Adapters\Interfaces\FileManipulationInterface;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Adapters\Interfaces\PathResolverInterface;

class CsvExporter
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly PathResolverInterface $paths,
        private readonly FileMetaInterface $fileMeta,
        private readonly FileManipulationInterface $fileManipulation
    ) {
    }

    /**
     * Export data to a file.
     */
    public function export(Exportable $exporter, ExportHistory $history): array
    {
        $path = $this->config->get('export.path', 'exports');
        $basePath = $this->paths->storagePath('app');

        $fullDir = $basePath . DIRECTORY_SEPARATOR . $path;

        if (!$this->fileMeta->isDir($fullDir)) {
            $this->fileManipulation->mkdir($fullDir, 0755, true);
        }

        $filename = $history->filename;
        $fullPath = $fullDir . DIRECTORY_SEPARATOR . $filename;
        $relativePath = $path . DIRECTORY_SEPARATOR . $filename;

        $handle = fopen($fullPath, 'w');

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
        if (method_exists($query, 'chunk')) {
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

        return [
            'path' => $relativePath,
            'rows_count' => $rowCount,
            'file_size' => $this->fileMeta->size($fullPath),
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

        if (method_exists($query, 'get')) {
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
