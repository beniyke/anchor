<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Referral code model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $user_id
 * @property string          $code
 * @property bool            $is_active
 * @property int             $uses_count
 * @property int             $max_uses
 * @property ?DateTimeHelper $expires_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read ModelCollection $referrals
 */
class ReferralCode extends BaseModel
{
    protected string $table = 'referral_code';

    protected array $fillable = [
        'user_id',
        'code',
        'is_active',
        'uses_count',
        'max_uses',
        'expires_at',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'is_active' => 'bool',
        'uses_count' => 'int',
        'max_uses' => 'int',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'code_id');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->max_uses > 0 && $this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->update([
            'uses_count' => $this->uses_count + 1,
        ]);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))->first();
    }
}
