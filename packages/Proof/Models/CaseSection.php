<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Case Section.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $proof_case_study_id
 * @property string          $title
 * @property string          $content
 * @property int             $order
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read CaseStudy $caseStudy
 */
class CaseSection extends BaseModel
{
    protected string $table = 'proof_case_section';

    protected array $fillable = [
        'proof_case_study_id',
        'title',
        'content',
        'order',
    ];

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class, 'proof_case_study_id');
    }
}
