<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Metric.
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
 * @property string          $label
 * @property string          $value
 * @property ?string         $prefix
 * @property ?string         $suffix
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read CaseStudy $caseStudy
 */
class Metric extends BaseModel
{
    protected string $table = 'proof_metric';

    protected array $fillable = [
        'proof_case_study_id',
        'label',
        'value',
        'prefix',
        'suffix',
    ];

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class, 'proof_case_study_id');
    }
}
