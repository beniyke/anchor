<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Source.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $email
 * @property ?string         $company
 * @property ?string         $job_title
 * @property ?string         $avatar_url
 * @property ?string         $linkedin_url
 * @property ?string         $website_url
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $testimonials
 * @property-read ModelCollection $caseStudies
 */
class Source extends BaseModel
{
    protected string $table = 'proof_source';

    protected array $fillable = [
        'name',
        'email',
        'company',
        'job_title',
        'avatar_url',
        'linkedin_url',
        'website_url',
    ];

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'proof_source_id');
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class, 'proof_source_id');
    }
}
