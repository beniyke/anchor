<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Task.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $onboard_template_id
 * @property string          $name
 * @property ?string         $description
 * @property int             $order
 * @property bool            $is_required
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Template $template
 * @property-read ModelCollection $completions
 */
class Task extends BaseModel
{
    protected string $table = 'onboard_task';

    protected array $fillable = [
        'onboard_template_id',
        'name',
        'description',
        'order',
        'is_required',
    ];

    protected array $casts = [
        'order' => 'integer',
        'is_required' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'onboard_template_id');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class, 'onboard_task_id');
    }
}
