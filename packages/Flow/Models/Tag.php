<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Tag
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsToMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $color
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $tasks
 */
class Tag extends BaseModel
{
    protected string $table = 'flow_tag';

    protected array $fillable = ['name', 'color'];

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'flow_task_tag', 'tag_id', 'task_id');
    }
}
