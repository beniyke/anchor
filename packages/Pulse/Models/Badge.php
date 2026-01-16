<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Badge.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsToMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property ?string         $description
 * @property ?string         $icon_url
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $users
 */
class Badge extends BaseModel
{
    protected string $table = 'pulse_badge';

    protected array $fillable = [
        'name',
        'description',
        'icon_url',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'pulse_user_badge',
            'pulse_badge_id',
            'user_id'
        )->withTimestamps();
    }
}
