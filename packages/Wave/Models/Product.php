<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Product Model for one-time purchases.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Traits\HasRefid;

/**
 * @property int     $id
 * @property string  $refid
 * @property string  $name
 * @property ?string $description
 * @property int     $price
 * @property string  $currency
 * @property string  $status
 * @property ?array  $metadata
 *
 * @method static Builder isActive()
 * @method static Builder isInactive()
 */
class Product extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_product';

    protected array $fillable = [
        'refid',
        'name',
        'description',
        'price',
        'currency',
        'status',
        'metadata'
    ];

    protected array $casts = [
        'id' => 'integer',
        'price' => 'integer',
        'metadata' => 'json'
    ];

    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeIsInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }
}
