<?php

declare(strict_types=1);

namespace Academy\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;

class AcademyRating extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_rating';

    protected string $refidPrefix = 'rat_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'program_id',
        'user_id',
        'rating',
        'review',
        'is_featured',
        'metadata',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'program_id' => 'integer',
        'user_id' => 'integer',
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(AcademyProgram::class, 'program_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
