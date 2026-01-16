<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Kpi.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $name
 * @property string             $slug
 * @property ?string            $description
 * @property ?string            $category
 * @property ?string            $unit
 * @property ?string            $frequency
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ModelCollection $values
 */
class Kpi extends BaseModel
{
    protected string $table = 'metric_kpi';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'unit',
        'frequency',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(KpiValue::class, 'metric_kpi_id');
    }
}
