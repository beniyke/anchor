<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Campaign.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Relations\HasOne;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $title
 * @property string          $subject
 * @property int             $template_id
 * @property string          $status
 * @property ?DateTimeHelper $scheduled_at
 * @property ?DateTimeHelper $sent_at
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Template $template
 * @property-read ModelCollection $events
 * @property-read ?Stat $stat
 */
class Campaign extends BaseModel
{
    protected string $table = 'blish_campaign';

    protected array $fillable = [
        'refid',
        'title',
        'subject',
        'template_id',
        'status',
        'scheduled_at',
        'sent_at',
        'metadata',
    ];

    protected array $casts = [
        'template_id' => 'integer',
        'metadata' => 'json',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'campaign_id');
    }

    public function stat(): HasOne
    {
        return $this->hasOne(Stat::class, 'campaign_id');
    }

    public function getOpenTrend(string $interval = 'day'): array
    {
        return $this->getEventTrend('open', $interval);
    }

    public function getClickTrend(string $interval = 'day'): array
    {
        return $this->getEventTrend('click', $interval);
    }

    protected function getEventTrend(string $type, string $interval = 'day'): array
    {
        // SQLite syntax for date formatting
        $dateFormat = match ($interval) {
            'hour' => "strftime('%Y-%m-%d %H:00:00', created_at)",
            'month' => "strftime('%Y-%m-01', created_at)",
            default => "strftime('%Y-%m-%d', created_at)", // day
        };

        $results = $this->events()
            ->where('type', $type)
            ->selectRaw("{$dateFormat} as date, count(*) as count")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return $results->pluck('count', 'date');
    }
}
