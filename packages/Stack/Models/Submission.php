<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Submission model for the Stack package.
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
 * @property string          $refid
 * @property int             $stack_form_id
 * @property int             $user_id
 * @property ?string         $ip_address
 * @property ?string         $user_agent
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Form $form
 * @property-read ModelCollection $values
 */
class Submission extends BaseModel
{
    protected string $table = 'stack_submission';

    protected array $fillable = [
        'refid',
        'stack_form_id',
        'user_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected array $casts = [
        'stack_form_id' => 'int',
        'user_id' => 'int',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'stack_form_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(SubmissionValue::class, 'stack_submission_id');
    }

    /**
     * Find by reference ID.
     */
}
