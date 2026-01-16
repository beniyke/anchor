<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Template.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $role
 * @property ?string         $description
 * @property bool            $is_active
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $tasks
 * @property-read ModelCollection $documents
 * @property-read ModelCollection $training
 */
class Template extends BaseModel
{
    protected string $table = 'onboard_template';

    protected array $fillable = [
        'name',
        'role',
        'description',
        'is_active',
    ];

    protected array $casts = [
        'is_active' => 'boolean',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'onboard_template_id')->orderBy('order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'onboard_template_id');
    }

    public function training(): HasMany
    {
        return $this->hasMany(Training::class, 'onboard_template_id')->orderBy('order');
    }
}
