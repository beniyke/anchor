<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Field model for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $stack_form_id
 * @property string          $name
 * @property string          $label
 * @property string          $type
 * @property ?string         $placeholder
 * @property ?array          $field_rules
 * @property int             $order_index
 * @property ?array          $settings
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Form $form
 * @property-read ModelCollection $options
 */
class Field extends BaseModel
{
    protected string $table = 'stack_field';

    protected array $fillable = [
        'stack_form_id',
        'name',
        'label',
        'type',
        'placeholder',
        'field_rules',
        'order_index',
        'settings',
    ];

    protected array $casts = [
        'stack_form_id' => 'int',
        'field_rules' => 'array',
        'order_index' => 'int',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'stack_form_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FieldOption::class, 'stack_field_id')->orderBy('order_index');
    }

    public function isMultipleChoice(): bool
    {
        return in_array($this->type, ['select', 'radio', 'checkbox']);
    }

    public function isFileUpload(): bool
    {
        return $this->type === 'file';
    }
}
