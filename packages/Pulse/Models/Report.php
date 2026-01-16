<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Report.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $reportable_type
 * @property int             $reportable_id
 * @property int             $user_id
 * @property string          $reason
 * @property string          $status
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read BaseModel $reportable
 */
class Report extends BaseModel
{
    protected string $table = 'pulse_report';

    protected array $fillable = [
        'reportable_type',
        'reportable_id',
        'user_id',
        'reason',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
