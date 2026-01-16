<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * SubmissionValue model for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $stack_submission_id
 * @property int             $stack_field_id
 * @property string          $value
 * @property ?int            $media_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Submission $submission
 * @property-read Field $field
 */
class SubmissionValue extends BaseModel
{
    protected string $table = 'stack_submission_value';

    protected array $fillable = [
        'stack_submission_id',
        'stack_field_id',
        'value',
        'media_id',
    ];

    protected array $casts = [
        'stack_submission_id' => 'int',
        'stack_field_id' => 'int',
        'media_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, 'stack_submission_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class, 'stack_field_id');
    }

    public function getArrayValue(): array
    {
        return json_decode($this->value ?? '[]', true) ?? [];
    }
}
