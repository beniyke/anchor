<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * FieldOption model for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $stack_field_id
 * @property string          $label
 * @property string          $value
 * @property int             $order_index
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Field $field
 */
class FieldOption extends BaseModel
{
    protected string $table = 'stack_field_option';

    protected array $fillable = [
        'stack_field_id',
        'label',
        'value',
        'order_index',
    ];

    protected array $casts = [
        'stack_field_id' => 'int',
        'order_index' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'stack_field_id');
    }
}
