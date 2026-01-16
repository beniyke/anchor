<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tax Rate Model for tax calculation logic.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Traits\HasRefid;

class TaxRate extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_tax_rate';

    protected array $fillable = [
        'refid',
        'name',
        'rate',
        'country',
        'state',
        'is_inclusive'
    ];

    protected array $casts = [
        'id' => 'integer',
        'rate' => 'float',
        'is_inclusive' => 'boolean'
    ];
}
