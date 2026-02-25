<?php

declare(strict_types=1);

namespace Academy\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;

class AcademyBadgeAward extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_badge_award';

    protected string $refidPrefix = 'bka_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'user_id',
        'badge_id',
        'program_id',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'user_id' => 'integer',
        'badge_id' => 'integer',
        'program_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(AcademyBadge::class, 'badge_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }
}
