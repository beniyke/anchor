<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Ticket category model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $name
 * @property string             $slug
 * @property ?string            $description
 * @property bool               $is_active
 * @property int                $display_order
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ModelCollection $tickets
 */
class TicketCategory extends BaseModel
{
    protected string $table = 'support_category';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'display_order',
    ];

    protected array $casts = [
        'is_active' => 'bool',
        'display_order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function scopeActive(bool $active = true): static
    {
        return $this->where('is_active', $active);
    }

    public function scopeInactive(): static
    {
        return $this->active(false);
    }

    public function scopeOrdered(): static
    {
        return $this->orderBy('display_order', 'asc');
    }
}
