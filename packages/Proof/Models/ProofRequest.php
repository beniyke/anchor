<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Proof Request.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $proof_source_id
 * @property string          $token
 * @property string          $status
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Source $source
 */
class ProofRequest extends BaseModel
{
    protected string $table = 'proof_request';

    protected array $fillable = [
        'proof_source_id',
        'token',
        'status',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'proof_source_id');
    }
}
