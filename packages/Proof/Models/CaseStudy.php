<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Case Study.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $proof_source_id
 * @property string          $title
 * @property string          $slug
 * @property string          $summary
 * @property string          $status
 * @property ?string         $featured_image
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Source $source
 * @property-read ModelCollection $sections
 * @property-read ModelCollection $metrics
 *
 * @method static Builder published()
 */
class CaseStudy extends BaseModel
{
    protected string $table = 'proof_case_study';

    protected array $fillable = [
        'proof_source_id',
        'title',
        'slug',
        'summary',
        'status',
        'featured_image',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'proof_source_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CaseSection::class, 'proof_case_study_id')->orderBy('order', 'asc');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class, 'proof_case_study_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
