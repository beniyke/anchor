<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core import manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Services;

use App\Services\Auth\Interfaces\AuthServiceInterface;
use Core\Services\ConfigServiceInterface;
use Exception;
use Helpers\DateTimeHelper;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use Helpers\File\Adapters\Interfaces\PathResolverInterface;
use Helpers\String\Str;
use Import\Contracts\Importable;
use Import\Enums\ImportStatus;
use Import\Models\ImportError;
use Import\Models\ImportHistory;
use Import\Services\Builders\ImportBuilder;
use Import\Services\Importers\CsvImporter;
use Import\Tasks\ProcessImportTask;
use Queue\Queue;

class ImportManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly AuthServiceInterface $auth,
        private readonly PathResolverInterface $paths,
        private readonly FileMetaInterface $fileMeta
    ) {
    }

    public function make(string|Importable $importer): ImportBuilder
    {
        if (is_string($importer)) {
            $importer = new $importer();
        }

        return new ImportBuilder($this, $importer);
    }

    public function queue(string|Importable $importer, string $filePath, array $options = []): ImportHistory
    {
        if (is_string($importer)) {
            $importerClass = $importer;
            $importer = new $importer();
        } else {
            $importerClass = get_class($importer);
        }

        $filename = basename($filePath);

        $history = ImportHistory::create([
            'refid' => Str::random('secure'),
            'user_id' => $this->auth->user()?->id,
            'importer_class' => $importerClass,
            'filename' => $filename,
            'original_filename' => $options['original_filename'] ?? $filename,
            'disk' => $this->config->get('import.disk', 'local'),
            'path' => $filePath,
            'status' => ImportStatus::PENDING,
        ]);

        // Dispatch task for background processing if queue enabled
        if ($this->config->get('import.queue.enabled', false)) {
            Queue::dispatch(ProcessImportTask::class, [
                'history_id' => $history->id,
            ]);

            return $history;
        }

        // Process synchronously if queue not enabled
        $this->processImport($history, $importer);

        return $history;
    }

    public function processImport(ImportHistory $history, ?Importable $importer = null): void
    {
        if (!$importer) {
            $importerClass = $history->importer_class;
            $importer = new $importerClass();
        }

        $csvImporter = resolve(CsvImporter::class);

        try {
            $csvImporter->import($importer, $history);
        } catch (Exception $e) {
            $history->markAsFailed($e->getMessage());
        }
    }

    public function getHistory(?int $userId = null): array
    {
        $query = ImportHistory::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function find(int $id): ?ImportHistory
    {
        return ImportHistory::find($id);
    }

    public function findByRefid(string $refid): ?ImportHistory
    {
        return ImportHistory::findByRefid($refid);
    }

    public function getErrors(ImportHistory $history): array
    {
        return $history->errors()->get()->toArray();
    }

    public function cleanup(?int $daysToRetain = null): int
    {
        $days = $daysToRetain ?? 30;

        if ($days <= 0) {
            return 0;
        }

        $cutoffDate = DateTimeHelper::now()->subDays($days);

        $oldImports = ImportHistory::where('created_at', '<', $cutoffDate)->get();
        $count = 0;

        foreach ($oldImports as $import) {
            ImportError::where('import_id', $import->id)->delete();

            // Delete file if exists
            if ($import->path && $this->fileMeta->exists($import->path)) {
                unlink($import->path);
            }

            $import->delete();
            $count++;
        }

        return $count;
    }
}
