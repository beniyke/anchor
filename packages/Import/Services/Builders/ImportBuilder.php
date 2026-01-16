<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent import builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Services\Builders;

use Import\Contracts\Importable;
use Import\Models\ImportHistory;
use Import\Services\ImportManagerService;
use RuntimeException;

class ImportBuilder
{
    private ?string $filePath = null;

    private ?string $originalFilename = null;

    private bool $queue = false;

    private bool $skipDuplicates = true;

    private bool $stopOnError = false;

    public function __construct(
        private readonly ImportManagerService $manager,
        private readonly Importable $importer
    ) {
    }

    public function file(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Set original filename (for display purposes).
     */
    public function originalFilename(string $filename): self
    {
        $this->originalFilename = $filename;

        return $this;
    }

    /**
     * Skip duplicate rows.
     */
    public function skipDuplicates(bool $skip = true): self
    {
        $this->skipDuplicates = $skip;

        return $this;
    }

    /**
     * Stop on first error.
     */
    public function stopOnError(bool $stop = true): self
    {
        $this->stopOnError = $stop;

        return $this;
    }

    /**
     * Queue the import for background processing.
     */
    public function queue(): self
    {
        $this->queue = true;

        return $this;
    }

    public function execute(): ImportHistory
    {
        if (!$this->filePath) {
            throw new RuntimeException('No file specified for import.');
        }

        return $this->manager->queue($this->importer, $this->filePath, [
            'original_filename' => $this->originalFilename,
            'skip_duplicates' => $this->skipDuplicates,
            'stop_on_error' => $this->stopOnError,
            'queue' => $this->queue,
        ]);
    }
}
