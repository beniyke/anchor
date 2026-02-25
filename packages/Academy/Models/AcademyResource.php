<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Models;

use Academy\Enums\ResourceType;
use Academy\Enums\VideoProvider;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;

class AcademyResource extends BaseModel
{
    use HasRefid;

    public const TABLE = 'academy_resource';

    protected string $refidPrefix = 'res_';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'lesson_id',
        'title',
        'type',
        'path',
        'provider',
        'sort_order',
    ];

    protected array $casts = [
        'id' => 'integer',
        'refid' => 'string',
        'lesson_id' => 'integer',
        'type' => ResourceType::class,
        'provider' => VideoProvider::class,
        'sort_order' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }
}
