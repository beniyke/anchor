<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Primary entry point for the Watcher system.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher;

use Helpers\String\UuidGenerator;
use Watcher\Batching\BatchRecorder;
use Watcher\Config\WatcherConfig;
use Watcher\Filters\WatcherFilter;
use Watcher\Sampling\Sampler;
use Watcher\Storage\WatcherRepository;

class WatcherManager
{
    private WatcherConfig $config;

    private WatcherRepository $repository;

    private ?BatchRecorder $batchRecorder = null;

    private Sampler $sampler;

    private WatcherFilter $filter;

    private ?string $currentBatchId = null;

    public function __construct(WatcherConfig $config, WatcherRepository $repository, Sampler $sampler, WatcherFilter $filter)
    {
        $this->config = $config;
        $this->repository = $repository;
        $this->sampler = $sampler;
        $this->filter = $filter;

        if ($config->isBatchingEnabled()) {
            $this->batchRecorder = new BatchRecorder($repository, $config);
        }
    }

    public function record(string $type, array $data): void
    {
        if (! $this->shouldRecord($type, $data)) {
            return;
        }

        $filteredData = $this->filter->filter($type, $data);

        $entry = [
            'batch_id' => $this->currentBatchId,
            'type' => $type,
            'family_hash' => $this->generateFamilyHash($type, $filteredData),
            'content' => json_encode($filteredData),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->batchRecorder) {
            $this->batchRecorder->add($entry);
        } else {
            $this->repository->insert($entry);
        }
    }

    public function startBatch(?string $batchId = null): void
    {
        $this->currentBatchId = $batchId ?? $this->generateBatchId();
    }

    public function stopBatch(): void
    {
        if ($this->batchRecorder) {
            $this->batchRecorder->flush();
        }
        $this->currentBatchId = null;
    }

    public function flush(): void
    {
        if ($this->batchRecorder) {
            $this->batchRecorder->flush();
        }
    }

    private function shouldRecord(string $type, array $data): bool
    {
        if (! $this->config->isEnabled() || ! $this->config->isTypeEnabled($type)) {
            return false;
        }

        if ($this->filter->shouldIgnore($type, $data)) {
            return false;
        }

        return $this->sampler->shouldSample($type);
    }

    private function generateFamilyHash(string $type, array $data): ?string
    {
        // Group similar entries together (e.g., same query pattern, same exception type)
        return match ($type) {
            'query' => md5($data['sql'] ?? ''),
            'exception' => md5(($data['class'] ?? '') . ($data['file'] ?? '') . ($data['line'] ?? '')),
            'request' => md5(($data['method'] ?? '') . ($data['uri'] ?? '')),
            'ghost' => md5(($data['action'] ?? '') . ($data['impersonator_id'] ?? '') . ($data['impersonated_id'] ?? '')),
            default => null,
        };
    }

    private function generateBatchId(): string
    {
        return UuidGenerator::v4();
    }
}
