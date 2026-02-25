<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Database\BaseModel;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;

class AcademyBadge extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_badge';

    protected string $table = self::TABLE;

    protected string $refidPrefix = 'bdg_';

    protected array $fillable = [
        'refid',
        'name',
        'slug',
        'description',
        'icon',
        'points_required',
    ];

    protected array $casts = [
        'id' => 'integer',
        'points_required' => 'integer',
    ];

    public function programs(): HasMany
    {
        return $this->hasMany(AcademyProgramBadge::class, 'badge_id');
    }
}
