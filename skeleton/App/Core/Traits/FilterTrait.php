<?php

declare(strict_types=1);

namespace App\Core\Traits;

trait FilterTrait
{
    protected array $filters = [];

    protected function getAllFilters(): array
    {
        return $this->filters;
    }

    protected function hasFilter(string|array $filter): bool
    {
        if (is_array($filter)) {
            foreach ($filter as $value) {
                if (! array_key_exists($value, $this->filters)) {
                    return false;
                }
            }

            return true;
        }

        return array_key_exists($filter, $this->filters);
    }

    protected function resetFilters(): self
    {
        $this->filters = [];

        return $this;
    }
}
