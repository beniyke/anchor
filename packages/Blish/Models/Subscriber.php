<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Subscriber.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsToMany;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $email
 * @property ?string         $name
 * @property string          $status
 * @property ?DateTimeHelper $verified_at
 * @property ?DateTimeHelper $unsubscribed_at
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $lists
 * @property-read ModelCollection $tags
 * @property-read ModelCollection $events
 */
class Subscriber extends BaseModel
{
    protected string $table = 'blish_subscriber';

    protected array $fillable = [
        'refid',
        'email',
        'name',
        'status',
        'verified_at',
        'unsubscribed_at',
        'metadata',
    ];

    protected array $casts = [
        'metadata' => 'json',
        'verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(BlishList::class, 'blish_list_subscriber', 'subscriber_id', 'list_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blish_subscriber_tag', 'subscriber_id', 'tag_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'subscriber_id');
    }
}
