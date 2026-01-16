<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Stat.
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
 * @property int             $recipients
 * @property int             $delivered
 * @property int             $opens
 * @property int             $unique_opens
 * @property int             $clicks
 * @property int             $unique_clicks
 * @property int             $unsubscribes
 * @property int             $bounces
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Campaign $campaign
 */
class Stat extends BaseModel
{
    protected string $table = 'blish_stat';

    protected array $fillable = [
        'campaign_id',
        'recipients',
        'delivered',
        'opens',
        'unique_opens',
        'clicks',
        'unique_clicks',
        'unsubscribes',
        'bounces',
    ];

    protected array $casts = [
        'campaign_id' => 'integer',
        'recipients' => 'integer',
        'delivered' => 'integer',
        'opens' => 'integer',
        'unique_opens' => 'integer',
        'clicks' => 'integer',
        'unique_clicks' => 'integer',
        'unsubscribes' => 'integer',
        'bounces' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
