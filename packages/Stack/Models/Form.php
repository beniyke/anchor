<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Form model for the Stack package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $title
 * @property string          $slug
 * @property ?string         $description
 * @property string          $status
 * @property ?array          $settings
 * @property int             $created_by
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $fields
 * @property-read ModelCollection $submissions
 * @property-read ModelCollection $events
 */
class Form extends BaseModel
{
    protected string $table = 'stack_form';

    protected array $fillable = [
        'refid',
        'title',
        'slug',
        'description',
        'status',
        'settings',
        'created_by',
    ];

    protected array $casts = [
        'status' => 'string',
        'settings' => 'array',
        'created_by' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class, 'stack_form_id')->orderBy('order_index');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'stack_form_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'stack_form_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
