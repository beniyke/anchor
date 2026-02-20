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
use Onboard\Enums\EquipmentStatus;

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
    public const TABLE = 'onboard_equipment';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'user_id',
        'request_type',
        'status',
        'asset_tag',
        'notes',
    ];

    protected array $casts = [
        'status' => EquipmentStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
