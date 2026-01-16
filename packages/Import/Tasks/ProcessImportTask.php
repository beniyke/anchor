<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task to process imports in background.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Tasks;

use Exception;
use Import\Models\ImportHistory;
use Import\Services\ImportManagerService;
use Queue\BaseTask;
use Queue\Scheduler;

class ProcessImportTask extends BaseTask
{
    /**
     * Run once (not recurring).
     */
    public function occurrence(): string
    {
        return self::once();
    }

    /**
     * Run immediately.
     */
    public function period(Scheduler $schedule): Scheduler
    {
        return $schedule;
    }

    /**
     * Execute the import processing.
     */
    protected function execute(): bool
    {
        $historyId = $this->payload->get('history_id');

        if (!$historyId) {
            return false;
        }

        $history = ImportHistory::find($historyId);

        if (!$history || !$history->isPending()) {
            return true; // Already processed or doesn't exist
        }

        try {
            $manager = resolve(ImportManagerService::class);
            $manager->processImport($history);

            return true;
        } catch (Exception $e) {
            $history->markAsFailed($e->getMessage());

            return false;
        }
    }

    /**
     * Success message.
     */
    protected function successMessage(): string
    {
        return 'Import processed successfully';
    }

    /**
     * Failed message.
     */
    protected function failedMessage(): string
    {
        return 'Import processing failed';
    }
}
