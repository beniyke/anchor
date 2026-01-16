<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link model for timed token-based access.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\HasMany;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $token
 * @property string          $linkable_type
 * @property int             $linkable_id
 * @property array           $scopes
 * @property ?string         $recipient_type
 * @property ?string         $recipient_value
 * @property ?int            $max_uses
 * @property int             $use_count
 * @property ?DateTimeHelper $expires_at
 * @property ?DateTimeHelper $revoked_at
 * @property ?array          $metadata
 * @property int             $created_by
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read BaseModel $linkable
 * @property-read ModelCollection $usages
 *
 * @method static Builder valid()
 * @method static Builder expired()
 * @method static Builder revoked()
 * @method static Builder forResource(string $type, int $id)
 * @method static Builder createdBy(int $userId)
 */
class Link extends BaseModel
{
    protected string $table = 'link';

    protected array $fillable = [
        'refid',
        'token',
        'linkable_type',
        'linkable_id',
        'scopes',
        'recipient_type',
        'recipient_value',
        'max_uses',
        'use_count',
        'expires_at',
        'revoked_at',
        'metadata',
        'created_by',
    ];

    protected array $casts = [
        'scopes' => 'array',
        'max_uses' => 'int',
        'use_count' => 'int',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'metadata' => 'array',
        'created_by' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected array $hidden = [
        'token',
    ];

    public function linkable(): MorphTo
    {
        return $this->morphTo('linkable');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(LinkUsage::class, 'link_id');
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isRevoked() && !$this->isExhausted();
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExhausted(): bool
    {
        if ($this->max_uses === null || $this->max_uses === 0) {
            return false;
        }

        return $this->use_count >= $this->max_uses;
    }

    public function getStatus(): LinkStatus
    {
        if ($this->isRevoked()) {
            return LinkStatus::REVOKED;
        }

        if ($this->isExpired()) {
            return LinkStatus::EXPIRED;
        }

        if ($this->isExhausted()) {
            return LinkStatus::EXHAUSTED;
        }

        return LinkStatus::ACTIVE;
    }

    public function hasScope(string|LinkScope $scope): bool
    {
        if ($scope instanceof LinkScope) {
            $scope = $scope->value;
        }

        return in_array($scope, $this->scopes ?? [], true);
    }

    public function canView(): bool
    {
        return $this->hasScope(LinkScope::VIEW);
    }

    public function canDownload(): bool
    {
        return $this->hasScope(LinkScope::DOWNLOAD);
    }

    public function canEdit(): bool
    {
        return $this->hasScope(LinkScope::EDIT);
    }

    public function canJoin(): bool
    {
        return $this->hasScope(LinkScope::JOIN);
    }

    public function canShare(): bool
    {
        return $this->hasScope(LinkScope::SHARE);
    }

    public function revoke(): void
    {
        $this->update([
            'revoked_at' => DateTimeHelper::now(),
        ]);
    }

    /**
     * Increment use count.
     */
    public function incrementUseCount(): void
    {
        $this->increment('use_count');
    }

    /**
     * Record usage with metadata (fluent).
     */
    public function recordUse(?string $ip = null, ?string $userAgent = null, array $extra = []): self
    {
        $this->incrementUseCount();

        LinkUsage::create([
            'link_id' => $this->id,
            'used_at' => DateTimeHelper::now(),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'metadata' => $extra,
        ]);

        return $this;
    }

    public function getRemainingUses(): ?int
    {
        if ($this->max_uses === null || $this->max_uses === 0) {
            return null; // Unlimited
        }

        return max(0, $this->max_uses - $this->use_count);
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public static function findByToken(string $tokenHash): ?self
    {
        return static::where('token', $tokenHash)->first();
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('revoked_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', DateTimeHelper::now());
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhere('max_uses', 0)
                    ->orWhereColumn('use_count', '<', 'max_uses');
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', DateTimeHelper::now());
    }

    public function scopeRevoked(Builder $query): Builder
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeForResource(Builder $query, string $type, int $id): Builder
    {
        return $query->where('linkable_type', $type)
            ->where('linkable_id', $id);
    }

    public function scopeCreatedBy(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }
}
