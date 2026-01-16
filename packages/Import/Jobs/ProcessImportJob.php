<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Job to process imports in background.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Jobs;

use Import\Models\ImportHistory;
use Import\Services\ImportManagerService;

class ProcessImportJob
{
    public function __construct(
        private readonly int $historyId
    ) {
    }

    public function handle(): void
    {
        $history = ImportHistory::find($this->historyId);

        if (!$history || !$history->isPending()) {
            return;
        }

        $manager = resolve(ImportManagerService::class);
        $manager->processImport($history);
    }

    public function getPayload(): array
    {
        return [
            'job' => static::class,
            'data' => [
                'history_id' => $this->historyId,
            ],
        ];
    }

    public static function fromPayload(array $data): self
    {
        return new self($data['history_id']);
    }
}
