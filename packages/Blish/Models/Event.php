<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Event.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $campaign_id
 * @property int             $subscriber_id
 * @property string          $type
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Campaign $campaign
 * @property-read Subscriber $subscriber
 */
class Event extends BaseModel
{
    protected string $table = 'blish_event';

    protected array $fillable = [
        'campaign_id',
        'subscriber_id',
        'type',
        'metadata',
    ];

    protected array $casts = [
        'campaign_id' => 'integer',
        'subscriber_id' => 'integer',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }
}
