<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Project
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $name
 * @property string          $description
 * @property int             $owner_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $owner
 * @property-read ModelCollection $columns
 * @property-read ModelCollection $tasks
 */
class Project extends BaseModel
{
    use HasRefid;

    protected string $table = 'flow_project';

    protected array $fillable = [
        'refid',
        'name',
        'description',
        'owner_id'
    ];

    protected array $casts = [
        'refid' => 'string'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class, 'project_id')->orderBy('order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }
}
