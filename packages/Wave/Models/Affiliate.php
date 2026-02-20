<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Affiliate Model for tracking partners and owners.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Traits\HasRefid;

class Affiliate extends BaseModel
{
    use HasRefid;
    public const TABLE = 'wave_affiliate';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'refid',
        'owner_id',
        'owner_type',
        'code',
        'status'
    ];

    public function referrals(): object
    {
        return $this->hasMany(Referral::class);
    }
}
