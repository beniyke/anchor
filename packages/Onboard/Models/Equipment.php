<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Equipment.
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
 * @property string          $request_type
 * @property string          $status
 * @property ?string         $asset_tag
 * @property ?string         $notes
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 */
class Equipment extends BaseModel
{
    protected string $table = 'onboard_equipment';

    protected array $fillable = [
        'user_id',
        'request_type',
        'status',
        'asset_tag',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
