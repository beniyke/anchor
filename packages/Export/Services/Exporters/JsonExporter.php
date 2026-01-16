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
use Helpers\File\Adapters\Interfaces\FileManipulationInterface;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Adapters\Interfaces\FileReadWriteInterface;
use Helpers\File\Adapters\Interfaces\PathResolverInterface;

class JsonExporter
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly PathResolverInterface $paths,
        private readonly FileMetaInterface $fileMeta,
        private readonly FileReadWriteInterface $fileReadWrite,
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

        $data = [];
        $headers = $exporter->headers();
        $query = $exporter->query();
        $rowCount = 0;

        // Handle both collection and query builder
        if (method_exists($query, 'get')) {
            $query = $query->get();
        }

        foreach ($query as $row) {
            $mapped = $exporter->map($row);
            $data[] = array_combine($headers, $mapped);
            $rowCount++;
        }

        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->fileReadWrite->put($fullPath, $content);

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
        $data = [];
        $headers = $exporter->headers();
        $query = $exporter->query();

        if (method_exists($query, 'get')) {
            $query = $query->get();
        }

        foreach ($query as $row) {
            $mapped = $exporter->map($row);
            $data[] = array_combine($headers, $mapped);
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
