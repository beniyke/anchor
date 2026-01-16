<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Feature model for feature flags.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $slug
 * @property ?string         $description
 * @property bool            $is_enabled
 * @property int             $percentage
 * @property ?DateTimeHelper $starts_at
 * @property ?DateTimeHelper $ends_at
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $segments
 *
 * @method static Builder active()
 * @method static Builder withPercentage()
 */
class Feature extends BaseModel
{
    protected string $table = 'rollout_feature';

    protected array $fillable = [
        'name',
        'slug',
        'description',
        'is_enabled',
        'percentage',
        'starts_at',
        'ends_at',
        'metadata',
    ];

    protected array $casts = [
        'is_enabled' => 'bool',
        'percentage' => 'int',
        'metadata' => 'json',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class, 'feature_id');
    }

    public function isActive(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        $now = DateTimeHelper::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        return true;
    }

    public function isInPercentage(User $user): bool
    {
        if ($this->percentage >= 100) {
            return true;
        }

        if ($this->percentage <= 0) {
            return false;
        }

        // Use consistent hashing based on user ID and feature slug
        $hash = crc32($user->id . ':' . $this->slug);
        $bucket = abs($hash % 100);

        return $bucket < $this->percentage;
    }

    public function isEnabledFor(?User $user = null, array $context = []): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->isInSegment($user, $context)) {
            return true;
        }

        if (!$user) {
            return $this->percentage >= 100;
        }

        // Check percentage rollout
        return $this->isInPercentage($user);
    }

    public function isInSegment(?User $user = null, array $context = []): bool
    {
        foreach ($this->segments()->get() as $segment) {
            if ($segment->matches($user, $context)) {
                return true;
            }
        }

        return false;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    public function scopeWithPercentage(Builder $query): Builder
    {
        return $query->where('percentage', '>', 0)->where('percentage', '<', 100);
    }
}
