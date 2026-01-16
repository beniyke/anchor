<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Blish List.
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
 * @property string          $refid
 * @property string          $name
 * @property string          $slug
 * @property ?string         $description
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $subscribers
 */
class BlishList extends BaseModel
{
    protected string $table = 'blish_list';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
        'description',
    ];

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'blish_list_subscriber', 'list_id', 'subscriber_id');
    }
}
