<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Kpi Builder Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services\Builders;

use Metric\Models\Kpi;
use RuntimeException;

class KpiBuilderService
{
    protected array $data = [
        'unit' => 'number',
        'frequency' => 'monthly',
    ];

    public function name(string $name): self
    {
        $this->data['name'] = $name;
        $this->data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

        return $this;
    }

    public function category(string $category): self
    {
        $this->data['category'] = $category;

        return $this;
    }

    public function unit(string $unit): self
    {
        $this->data['unit'] = $unit;

        return $this;
    }

    public function quarterly(): self
    {
        $this->data['frequency'] = 'quarterly';

        return $this;
    }

    public function create(): Kpi
    {
        if (empty($this->data['name'])) {
            throw new RuntimeException("KPI requires a name.");
        }

        return Kpi::create($this->data);
    }
}
