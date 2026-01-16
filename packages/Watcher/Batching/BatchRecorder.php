<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Batches Watcher entries before writing to storage.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Batching;

use Watcher\Config\WatcherConfig;
use Watcher\Storage\WatcherRepository;

class BatchRecorder
{
    private WatcherRepository $repository;

    private WatcherConfig $config;

    private array $batch = [];

    private ?float $lastFlush = null;

    public function __construct(WatcherRepository $repository, WatcherConfig $config)
    {
        $this->repository = $repository;
        $this->config = $config;
        $this->lastFlush = microtime(true);
    }

    public function add(array $entry): void
    {
        $this->batch[] = $entry;

        if ($this->shouldFlush()) {
            $this->flush();
        }
    }

    public function flush(): void
    {
        if (empty($this->batch)) {
            return;
        }

        $this->repository->insertBatch($this->batch);
        $this->batch = [];
        $this->lastFlush = microtime(true);
    }

    private function shouldFlush(): bool
    {
        $batchSize = count($this->batch);
        $maxSize = $this->config->getBatchSize();

        if ($batchSize >= $maxSize) {
            return true;
        }

        $elapsed = microtime(true) - $this->lastFlush;
        $flushInterval = $this->config->getBatchFlushInterval();

        return $elapsed >= $flushInterval;
    }

    public function __destruct()
    {
        $this->flush();
    }
}
