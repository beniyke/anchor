<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link usage tracking model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int            $id
 * @property int            $link_id
 * @property DateTimeHelper $used_at
 * @property ?string        $ip_address
 * @property ?string        $user_agent
 * @property ?array         $metadata
 * @property-read Link $link
 */
class LinkUsage extends BaseModel
{
    protected string $table = 'link_usage';

    protected array $fillable = [
        'link_id',
        'used_at',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected array $casts = [
        'link_id' => 'int',
        'used_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Disable default timestamps.
     */
    public bool $timestamps = false;

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id');
    }

    public function scopeForLink(int $linkId): static
    {
        return $this->where('link_id', $linkId);
    }

    public function scopeBetween(string $start, string $end): static
    {
        return $this->whereBetween('used_at', [$start, $end]);
    }
}
