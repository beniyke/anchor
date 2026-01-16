<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Stage.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $name
 * @property string             $slug
 * @property int                $order
 * @property bool               $is_default
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ModelCollection $applications
 */
class Stage extends BaseModel
{
    protected string $table = 'scout_stage';

    protected array $fillable = [
        'name',
        'slug',
        'order',
        'is_default',
    ];

    protected array $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'scout_stage_id');
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
