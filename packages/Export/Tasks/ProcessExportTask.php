<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task to process exports in background.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Tasks;

use Exception;
use Export\Models\ExportHistory;
use Export\Services\ExportManagerService;
use Queue\BaseTask;
use Queue\Scheduler;

class ProcessExportTask extends BaseTask
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
     * Execute the export processing.
     */
    protected function execute(): bool
    {
        $historyId = $this->payload->get('history_id');

        if (!$historyId) {
            return false;
        }

        $history = ExportHistory::find($historyId);

        if (!$history || !$history->isPending()) {
            return true; // Already processed or doesn't exist
        }

        try {
            $manager = resolve(ExportManagerService::class);
            $manager->processExport($history);

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
        return 'Export processed successfully';
    }

    /**
     * Failed message.
     */
    protected function failedMessage(): string
    {
        return 'Export processing failed';
    }
}
