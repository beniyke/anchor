<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Repository implementation for storing workflow history in the database.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Workflow\Infrastructure;

use Database\DB;
use Helpers\DateTimeHelper;
use Workflow\Contracts\History;

class DatabaseHistoryRepository implements History
{
    private const TABLE_NAME = 'workflow_history';

    public function getHistory(string $instanceId): array
    {
        $events = DB::table(self::TABLE_NAME)
            ->where('instance_id', $instanceId)
            ->oldest('id')
            ->get();

        return array_map(function ($event) {
            return [
                'type' => $event['type'],
                'payload' => $event['payload'] ? json_decode($event['payload'], true) : [],
                'result' => $event['result'] ? json_decode($event['result'], true) : null,
                'workflow_class' => $event['workflow_class'],
                'input' => $event['input'] ? json_decode($event['input'], true) : [],
                'created_at' => $event['created_at'],
            ];
        }, $events);
    }

    public function recordEvent(string $instanceId, string $eventType, array $payload = []): void
    {
        DB::table(self::TABLE_NAME)->insert([
            'instance_id' => $instanceId,
            'type' => $eventType,
            'payload' => json_encode($payload),
            'result' => isset($payload['result']) ? json_encode($payload['result']) : null,
            'created_at' => DateTimeHelper::now()->toDateTimeString(),
        ]);
    }

    public function createNewInstance(string $workflowClass, string $businessKey, array $input): string
    {
        $instanceId = $this->generateInstanceId();

        DB::table(self::TABLE_NAME)->insert([
            'instance_id' => $instanceId,
            'type' => 'WorkflowStarted',
            'payload' => json_encode(['business_key' => $businessKey]),
            'workflow_class' => $workflowClass,
            'input' => json_encode($input),
            'created_at' => DateTimeHelper::now()->toDateTimeString(),
        ]);

        return $instanceId;
    }

    public function findActiveInstanceIdByBusinessKey(string $key): ?string
    {
        $query = '"business_key":"$key"';

        $result = DB::table(self::TABLE_NAME)
            ->whereLike('payload', "%{$query}%")
            ->where('type', 'WorkflowStarted')
            ->latest('id')
            ->first();

        return $result ? $result->instance_id : null;
    }

    private function generateInstanceId(): string
    {
        return uniqid('wf_', true);
    }

    /**
     * Alias for createNewInstance for backward compatibility
     */
    public function createInstance(string $instanceId, string $workflowClass, array $input): void
    {
        DB::table(self::TABLE_NAME)->insert([
            'instance_id' => $instanceId,
            'type' => 'WorkflowStarted',
            'payload' => json_encode([]),
            'workflow_class' => $workflowClass,
            'input' => json_encode($input),
            'created_at' => DateTimeHelper::now()->toDateTimeString(),
        ]);
    }
}
