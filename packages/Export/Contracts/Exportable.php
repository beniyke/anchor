<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Exportable contract for export definitions.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Export\Contracts;

interface Exportable
{
    public function query(): mixed;

    public function headers(): array;

    /**
     * Map a row of data.
     */
    public function map(mixed $row): array;

    public function filename(): string;
}
