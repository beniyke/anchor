<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * JSON exporter service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Services\Exporters;

use Core\Services\ConfigServiceInterface;
use Export\Contracts\Exportable;
use Export\Models\ExportHistory;
use Helpers\File\Storage\Storage;

class JsonExporter
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

        $data = [];
        $headers = $exporter->headers();
        $query = $exporter->query();
        $rowCount = 0;

        // Handle both collection and query builder
        if (is_object($query) && method_exists($query, 'get')) {
            $query = $query->get();
        }

        foreach ($query as $row) {
            $mapped = $exporter->map($row);
            $data[] = array_combine($headers, $mapped);
            $rowCount++;
        }

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        Storage::put($relativePath, $content);

        return [
            'path' => $relativePath,
            'rows_count' => $rowCount,
            'file_size' => Storage::size($relativePath),
        ];
    }

    /**
     * Export to string (for direct download).
     */
    public function exportToString(Exportable $exporter): string
    {
        $data = [];
        $headers = $exporter->headers();
        $query = $exporter->query();

        if (is_object($query) && method_exists($query, 'get')) {
            $query = $query->get();
        }

        foreach ($query as $row) {
            $mapped = $exporter->map($row);
            $data[] = array_combine($headers, $mapped);
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
