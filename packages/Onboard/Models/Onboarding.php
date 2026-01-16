<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboarding.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $user_id
 * @property int             $onboard_template_id
 * @property string          $status
 * @property ?DateTimeHelper $started_at
 * @property ?DateTimeHelper $completed_at
 * @property ?DateTimeHelper $due_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read Template $template
 */
class Onboarding extends BaseModel
{
    protected string $table = 'onboard_onboarding';

    protected array $fillable = [
        'user_id',
        'onboard_template_id',
        'status',
        'started_at',
        'completed_at',
        'due_at',
    ];

    protected array $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'onboard_template_id');
    }
}
