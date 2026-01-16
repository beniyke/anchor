<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core export manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Services;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Core\Services\ConfigServiceInterface;
use Exception;
use Export\Contracts\Exportable;
use Export\Enums\ExportFormat;
use Export\Enums\ExportStatus;
use Export\Models\ExportHistory;
use Export\Services\Builders\ExportBuilder;
use Export\Services\Exporters\CsvExporter;
use Export\Services\Exporters\JsonExporter;
use Export\Tasks\ProcessExportTask;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Queue\Queue;
use RuntimeException;

class ExportManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly AuthServiceInterface $auth
    ) {
    }

    public function make(string|Exportable $exporter): ExportBuilder
    {
        if (is_string($exporter)) {
            $exporter = new $exporter();
        }

        return new ExportBuilder($this, $exporter);
    }

    public function queue(string|Exportable $exporter, array $options = []): ExportHistory
    {
        if (is_string($exporter)) {
            $exporterClass = $exporter;
            $exporter = new $exporter();
        } else {
            $exporterClass = get_class($exporter);
        }

        $format = $options['format'] ?? ExportFormat::CSV;
        $filename = $options['filename'] ?? $exporter->filename() . '.' . $format->value;

        $history = ExportHistory::create([
            'refid' => Str::random('secure'),
            'user_id' => $this->auth->user()?->id,
            'exporter_class' => $exporterClass,
            'format' => $format,
            'filename' => $filename,
            'disk' => $this->config->get('export.disk', 'local'),
            'status' => ExportStatus::PENDING,
        ]);

        // Dispatch task for background processing if queue enabled
        if ($this->config->get('export.queue.enabled', false)) {
            Queue::dispatch(ProcessExportTask::class, [
                'history_id' => $history->id,
            ]);

            return $history;
        }

        // Process synchronously if queue not enabled
        $this->processExport($history, $exporter);

        return $history;
    }

    public function processExport(ExportHistory $history, ?Exportable $exporter = null): void
    {
        if (!$exporter) {
            $exporterClass = $history->exporter_class;
            $exporter = new $exporterClass();
        }

        $history->markAsProcessing();

        try {
            $exporterService = match ($history->format) {
                ExportFormat::CSV => resolve(CsvExporter::class),
                ExportFormat::JSON => resolve(JsonExporter::class),
                default => resolve(CsvExporter::class),
            };

            $result = $exporterService->export($exporter, $history);

            $history->markAsCompleted(
                $result['path'],
                $result['rows_count'],
                $result['file_size']
            );
        } catch (Exception $e) {
            $history->markAsFailed($e->getMessage());
        }
    }

    public function getHistory(?int $userId = null): array
    {
        $query = ExportHistory::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function find(int $id): ?ExportHistory
    {
        return ExportHistory::find($id);
    }

    public function findByRefid(string $refid): ?ExportHistory
    {
        return ExportHistory::findByRefid($refid);
    }

    public function download(ExportHistory $export): array
    {
        if (!$export->isCompleted()) {
            throw new RuntimeException('Export is not ready for download.');
        }

        $disk = $this->config->get('export.disk', 'local');
        $basePath = $this->config->get('filesystems.disks.' . $disk . '.root', '');
        $fullPath = $basePath . DIRECTORY_SEPARATOR . $export->path;

        if (!file_exists($fullPath)) {
            throw new RuntimeException('Export file not found.');
        }

        return [
            'path' => $fullPath,
            'filename' => $export->filename,
            'mime_type' => $this->getMimeType($export->format),
        ];
    }

    public function cleanup(?int $daysToRetain = null): int
    {
        $days = $daysToRetain ?? $this->config->get('export.retention_days', 7);

        if ($days <= 0) {
            return 0;
        }

        $cutoffDate = DateTimeHelper::now()->subDays($days);

        $oldExports = ExportHistory::where('created_at', '<', $cutoffDate)->get();
        $count = 0;

        foreach ($oldExports as $export) {
            // Delete file if exists
            if ($export->path) {
                $disk = $this->config->get('export.disk', 'local');
                $basePath = $this->config->get('filesystems.disks.' . $disk . '.root', '');
                $fullPath = $basePath . DIRECTORY_SEPARATOR . $export->path;

                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $export->delete();
            $count++;
        }

        return $count;
    }

    private function getMimeType(ExportFormat $format): string
    {
        return match ($format) {
            ExportFormat::CSV => 'text/csv',
            ExportFormat::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ExportFormat::PDF => 'application/pdf',
            ExportFormat::JSON => 'application/json',
        };
    }
}
