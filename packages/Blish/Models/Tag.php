<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Tag.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsToMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $slug
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $subscribers
 */
class Tag extends BaseModel
{
    protected string $table = 'blish_tag';

    protected array $fillable = [
        'name',
        'slug',
    ];

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'blish_subscriber_tag', 'tag_id', 'subscriber_id');
    }
}
