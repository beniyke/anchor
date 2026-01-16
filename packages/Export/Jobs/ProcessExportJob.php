<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Job to process exports in background.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Jobs;

use Export\Models\ExportHistory;
use Export\Services\ExportManagerService;

class ProcessExportJob
{
    public function __construct(
        private readonly int $historyId
    ) {
    }

    public function handle(): void
    {
        $history = ExportHistory::find($this->historyId);

        if (!$history || !$history->isPending()) {
            return;
        }

        $manager = resolve(ExportManagerService::class);
        $manager->processExport($history);
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
