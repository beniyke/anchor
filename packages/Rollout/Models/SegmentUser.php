<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * SegmentUser pivot model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $segment_id
 * @property int $user_id
 * @property-read Segment $segment
 */
class SegmentUser extends BaseModel
{
    public const TABLE = 'rollout_segment_user';

    protected string $table = self::TABLE;

    public bool $timestamps = false;

    protected array $fillable = [
        'segment_id',
        'user_id',
    ];

    protected array $casts = [
        'segment_id' => 'int',
        'user_id' => 'int',
    ];

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }
}
