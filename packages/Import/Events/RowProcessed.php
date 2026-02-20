<?php

declare(strict_types=1);

namespace Import\Events;

use Import\Models\ImportHistory;

class RowProcessed
{
    public function __construct(
        public readonly ImportHistory $history,
        public readonly int $currentRow,
        public readonly int $totalRows,
        public readonly string $status = 'success'
    ) {
    }
}
