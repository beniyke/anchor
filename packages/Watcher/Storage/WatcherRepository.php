<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Repository for storing Watcher entries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Storage;

use Database\ConnectionInterface;
use Helpers\DateTimeHelper;
use PDOException;

class WatcherRepository
{
    private const WATCHER_TABLE = 'watcher_entry';

    private ConnectionInterface $connection;

    private ?bool $tableExists = null;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function insert(array $entry): void
    {
        if (!$this->ensureTableExists()) {
            return;
        }

        try {
            $this->connection->table(self::WATCHER_TABLE)->insert($entry);
        } catch (PDOException $e) {
            $this->tableExists = false;
        }
    }

    public function insertBatch(array $entries): void
    {
        if (empty($entries) || !$this->ensureTableExists()) {
            return;
        }

        try {
            $this->connection->table(self::WATCHER_TABLE)->insert($entries);
        } catch (PDOException $e) {
            $this->tableExists = false;
        }
    }

    public function getByType(string $type, int $limit = 100): array
    {
        if (!$this->ensureTableExists()) {
            return [];
        }

        try {
            return $this->connection->table(self::WATCHER_TABLE)
                ->where('type', $type)
                ->latest()
                ->limit($limit)
                ->get();
        } catch (PDOException $e) {
            $this->tableExists = false;

            return [];
        }
    }

    public function getByBatchId(string $batchId): array
    {
        if (!$this->ensureTableExists()) {
            return [];
        }

        try {
            return $this->connection->table(self::WATCHER_TABLE)
                ->where('batch_id', $batchId)
                ->oldest()
                ->get();
        } catch (PDOException $e) {
            $this->tableExists = false;

            return [];
        }
    }

    public function deleteOlderThan(string $type, int $days): int
    {
        if (!$this->ensureTableExists()) {
            return 0;
        }

        try {
            $date = DateTimeHelper::now()->subDays($days)->format('Y-m-d H:i:s');

            return $this->connection->table(self::WATCHER_TABLE)
                ->where('type', $type)
                ->whereBefore('created_at', $date)
                ->delete();
        } catch (PDOException $e) {
            $this->tableExists = false;

            return 0;
        }
    }

    public function countByType(string $type, ?string $since = null): int
    {
        if (!$this->ensureTableExists()) {
            return 0;
        }

        try {
            $query = $this->connection->table(self::WATCHER_TABLE)
                ->where('type', $type);

            if ($since) {
                $query->whereOnOrAfter('created_at', $since);
            }

            return $query->count();
        } catch (PDOException $e) {
            $this->tableExists = false;

            return 0;
        }
    }

    public function getStats(string $type, string $since): array
    {
        if (!$this->ensureTableExists()) {
            return ['count' => 0, 'entries' => []];
        }

        try {
            $entries = $this->connection->table(self::WATCHER_TABLE)
                ->where('type', $type)
                ->whereOnOrAfter('created_at', $since)
                ->get();

            return [
                'count' => count($entries),
                'entries' => $entries,
            ];
        } catch (PDOException $e) {
            $this->tableExists = false;

            return ['count' => 0, 'entries' => []];
        }
    }

    private function ensureTableExists(): bool
    {
        if ($this->tableExists !== null) {
            return $this->tableExists;
        }

        try {
            $this->connection->table(self::WATCHER_TABLE)->limit(1)->get();
            $this->tableExists = true;

            return true;
        } catch (PDOException $e) {
            $this->tableExists = false;

            return false;
        }
    }
}
