<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Column
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $project_id
 * @property string          $name
 * @property int             $order
 * @property string          $type
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Project $project
 * @property-read ModelCollection $tasks
 */
class Column extends BaseModel
{
    protected string $table = 'flow_column';

    protected array $fillable = [
        'project_id',
        'name',
        'order',
        'type'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'column_id')->orderBy('order');
    }
}
