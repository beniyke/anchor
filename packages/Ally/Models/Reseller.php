<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Reseller Model represents a partner who can manage clients
 * and distribute licenses.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Models;

use Ally\Enums\ResellerTier;
use App\Models\User;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $user_id
 * @property string          $company_name
 * @property ResellerTier    $tier
 * @property string          $status
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 *
 * @method static Builder active()
 * @method static Builder platinum()
 * @method static Builder gold()
 * @method static Builder standard()
 * @method static Builder status(string $status)
 */
class Reseller extends BaseModel
{
    use HasRefid;

    protected string $table = 'ally_reseller';

    protected array $fillable = [
        'refid',
        'user_id',
        'company_name',
        'tier',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'metadata' => 'json',
        'tier' => ResellerTier::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePlatinum(Builder $query): Builder
    {
        return $query->where('tier', ResellerTier::PLATINUM);
    }

    public function scopeGold(Builder $query): Builder
    {
        return $query->where('tier', ResellerTier::GOLD);
    }

    public function scopeStandard(Builder $query): Builder
    {
        return $query->where('tier', ResellerTier::STANDARD);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function isPlatinum(): bool
    {
        return $this->tier === ResellerTier::PLATINUM;
    }

    public function isGold(): bool
    {
        return $this->tier === ResellerTier::GOLD;
    }

    public function isStandard(): bool
    {
        return $this->tier === ResellerTier::STANDARD;
    }

    public function calculateTierCost(int $baseCost): int
    {
        return $this->tier->calculateCost($baseCost);
    }

    /**
     * Update a specific key in the metadata JSON.
     */
    public function updateMetadata(string $key, mixed $value): bool
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;

        return $this->save();
    }
}
