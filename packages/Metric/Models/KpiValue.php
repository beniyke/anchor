<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Kpi Value.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $metric_kpi_id
 * @property int                $user_id
 * @property float              $value
 * @property ?DateTimeInterface $measured_at
 * @property ?array             $meta
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Kpi $kpi
 * @property-read User $user
 */
class KpiValue extends BaseModel
{
    protected string $table = 'metric_kpi_value';

    protected array $fillable = [
        'metric_kpi_id',
        'user_id',
        'value',
        'measured_at',
        'meta',
    ];

    protected array $casts = [
        'value' => 'float',
        'measured_at' => 'datetime',
        'meta' => 'json',
    ];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'metric_kpi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
