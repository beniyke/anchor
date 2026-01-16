<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Job.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $title
 * @property string          $slug
 * @property string          $description
 * @property ?string         $requirements
 * @property ?string         $location
 * @property string          $type
 * @property ?string         $salary_range
 * @property string          $status
 * @property ?DateTimeHelper $expires_at
 * @property ?array          $meta
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $applications
 */
class Job extends BaseModel
{
    protected string $table = 'scout_job';

    protected array $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'location',
        'type',
        'salary_range',
        'status',
        'expires_at',
        'meta',
    ];

    protected array $casts = [
        'expires_at' => 'datetime',
        'meta' => 'json',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'scout_job_id');
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
