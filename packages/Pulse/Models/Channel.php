<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Channel.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $slug
 * @property ?string         $description
 * @property ?int            $parent_id
 * @property int             $order
 * @property bool            $is_private
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $threads
 * @property-read ?Channel $parent
 * @property-read ModelCollection $children
 */
class Channel extends BaseModel
{
    protected string $table = 'pulse_channel';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'is_private',
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'pulse_channel_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
