<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Event model for the Stack package analytics.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $stack_form_id
 * @property string          $event_type
 * @property ?int            $user_id
 * @property ?string         $session_id
 * @property ?array          $data
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Form $form
 */
class Event extends BaseModel
{
    protected string $table = 'stack_event';

    protected array $fillable = [
        'stack_form_id',
        'event_type',
        'user_id',
        'session_id',
        'data',
    ];

    protected array $casts = [
        'stack_form_id' => 'int',
        'user_id' => 'int',
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'stack_form_id');
    }
}
