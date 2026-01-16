<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Client Model represents a standalone entity that can be managed
 * independently, associated with a reseller, or linked to support tickets.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Models;

use App\Models\User;
use Client\Enums\ClientStatus;
use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $name
 * @property string          $email
 * @property ?string         $phone
 * @property ClientStatus    $status
 * @property ?array          $metadata
 * @property ?int            $owner_id
 * @property ?int            $user_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 *
 * @method static Builder active()
 * @method static Builder pending()
 * @method static Builder suspended()
 * @method static Builder standalone()
 * @method static Builder inactive()
 * @method static Builder reselled()
 *
 * @property-read ?User $reseller
 * @property-read ?User $user
 */
class Client extends BaseModel
{
    use HasRefid;

    protected string $table = 'client';

    protected array $fillable = [
        'refid',
        'name',
        'email',
        'phone',
        'status',
        'metadata',
        'owner_id', // Reseller (User)
        'user_id',  // Associated User account (optional)
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => ClientStatus::class,
        'metadata' => 'json',
        'owner_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Active);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Pending);
    }

    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Suspended);
    }

    public function scopeStandalone(Builder $query): Builder
    {
        return $query->whereNull('owner_id');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', ClientStatus::Inactive);
    }

    public function scopeReselled(Builder $query): Builder
    {
        return $query->whereNotNull('owner_id');
    }

    public function isActive(): bool
    {
        return $this->status === ClientStatus::Active;
    }

    public function isStandalone(): bool
    {
        return is_null($this->owner_id);
    }
}
