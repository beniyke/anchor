<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent export builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Services\Builders;

use Core\Services\ConfigServiceInterface;
use Export\Contracts\Exportable;
use Export\Enums\ExportFormat;
use Export\Models\ExportHistory;
use Export\Services\Exporters\CsvExporter;
use Export\Services\Exporters\JsonExporter;
use Export\Services\ExportManagerService;

class ExportBuilder
{
    private ExportFormat $format = ExportFormat::CSV;

    private ?string $filename = null;

    private ?string $disk = null;

    private bool $queue = false;

    public function __construct(
        private readonly ExportManagerService $manager,
        private readonly Exportable $exporter
    ) {
    }

    public function format(string|ExportFormat $format): self
    {
        if (is_string($format)) {
            $format = ExportFormat::from($format);
        }

        $this->format = $format;

        return $this;
    }

    public function csv(): self
    {
        $this->format = ExportFormat::CSV;

        return $this;
    }

    public function json(): self
    {
        $this->format = ExportFormat::JSON;

        return $this;
    }

    public function filename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function disk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Queue the export for background processing.
     */
    public function queue(): self
    {
        $this->queue = true;

        return $this;
    }

    public function execute(): ExportHistory
    {
        return $this->manager->queue($this->exporter, [
            'format' => $this->format,
            'filename' => $this->filename,
            'disk' => $this->disk,
            'queue' => $this->queue,
        ]);
    }

    /**
     * Download the export directly (without storing).
     */
    public function download(): array
    {
        $config = resolve(ConfigServiceInterface::class);

        $exporterService = match ($this->format) {
            ExportFormat::CSV => resolve(CsvExporter::class),
            ExportFormat::JSON => resolve(JsonExporter::class),
            default => resolve(CsvExporter::class),
        };

        $content = $exporterService->exportToString($this->exporter);
        $filename = $this->filename ?? $this->exporter->filename() . '.' . $this->format->value;

        return [
            'content' => $content,
            'filename' => $filename,
            'mime_type' => $this->getMimeType(),
        ];
    }

    private function getMimeType(): string
    {
        return match ($this->format) {
            ExportFormat::CSV => 'text/csv',
            ExportFormat::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ExportFormat::PDF => 'application/pdf',
            ExportFormat::JSON => 'application/json',
        };
    }
}
