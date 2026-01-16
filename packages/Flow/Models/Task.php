<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Task
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\BelongsToMany;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int                $id
 * @property int                $project_id
 * @property string             $refid
 * @property int                $column_id
 * @property ?int               $parent_id
 * @property int                $creator_id
 * @property string             $title
 * @property ?string            $description
 * @property string             $priority
 * @property string             $type
 * @property ?DateTimeInterface $due_date
 * @property ?DateTimeInterface $start_date
 * @property bool               $is_recurring
 * @property ?string            $recurrence_pattern
 * @property ?DateTimeHelper    $next_recurrence_at
 * @property int                $order
 * @property ?DateTimeHelper    $created_at
 * @property ?DateTimeHelper    $updated_at
 * @property-read Project $project
 * @property-read User $creator
 * @property-read Column $column
 * @property-read ?Task $parent
 * @property-read ModelCollection $subtasks
 * @property-read ModelCollection $assignees
 * @property-read ModelCollection $dependencies
 * @property-read ModelCollection $dependents
 * @property-read ModelCollection $attachments
 * @property-read ModelCollection $comments
 * @property-read ModelCollection $tags
 */
class Task extends BaseModel
{
    use HasRefid;

    protected string $table = 'flow_task';

    protected array $fillable = [
        'project_id',
        'refid',
        'column_id',
        'parent_id',
        'creator_id',
        'title',
        'description',
        'priority', // low, medium, high, urgent
        'type',     // task, bug, issue, idea
        'due_date',
        'start_date',
        'is_recurring',
        'recurrence_pattern', // JSON
        'next_recurrence_at',
        'order'
    ];

    protected array $casts = [
        'refid' => 'string',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'string',
        'due_date' => 'datetime',
        'start_date' => 'datetime',
        'next_recurrence_at' => 'datetime'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class, 'column_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'flow_task_assignee', 'task_id', 'user_id');
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'flow_dependency', 'task_id', 'depends_on_task_id');
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'flow_dependency', 'depends_on_task_id', 'task_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'task_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'flow_task_tag', 'task_id', 'tag_id');
    }
}
