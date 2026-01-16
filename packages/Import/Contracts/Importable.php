<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Importable contract for import definitions.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Contracts;

interface Importable
{
    public function model(): string;

    /**
     * Map a row of data to model attributes.
     */
    public function map(array $row): array;

    public function rules(): array;

    public function messages(): array;

    public function headers(): array;

    /**
     * Handle a single row after validation.
     * Return null to skip, or throw to fail.
     */
    public function handle(array $row): mixed;
}
