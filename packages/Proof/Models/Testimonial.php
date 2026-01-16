<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Testimonial.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $proof_source_id
 * @property string          $content
 * @property int             $rating
 * @property string          $status
 * @property ?string         $video_url
 * @property bool            $is_featured
 * @property bool            $is_verified
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Source $source
 *
 * @method static Builder approved()
 */
class Testimonial extends BaseModel
{
    protected string $table = 'proof_testimonial';

    protected array $fillable = [
        'proof_source_id',
        'content',
        'rating',
        'status',
        'video_url',
        'is_featured',
        'is_verified',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'proof_source_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }
}
