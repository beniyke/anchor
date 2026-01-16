<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ImportError model to track import row errors.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Import\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $import_id
 * @property int             $row_number
 * @property ?string         $column
 * @property ?string         $value
 * @property string          $error
 * @property ?array          $row_data
 * @property ?DateTimeHelper $created_at
 * @property-read ImportHistory $import
 */
class ImportError extends BaseModel
{
    protected string $table = 'import_error';

    protected array $fillable = [
        'import_id',
        'row_number',
        'column',
        'value',
        'error',
        'row_data',
    ];

    protected array $casts = [
        'import_id' => 'int',
        'row_number' => 'int',
        'row_data' => 'array',
        'created_at' => 'datetime',
    ];

    public bool $timestamps = false;

    public function import(): BelongsTo
    {
        return $this->belongsTo(ImportHistory::class, 'import_id');
    }
}
