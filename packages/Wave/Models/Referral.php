<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Referral Model for tracking affiliate success.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Traits\HasRefid;

class Referral extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_referral';

    protected array $fillable = [
        'refid',
        'affiliate_id',
        'referred_owner_id',
        'referred_owner_type',
        'commission_amount',
        'status'
    ];

    protected array $casts = [
        'id' => 'integer',
        'commission_amount' => 'integer'
    ];

    public function affiliate(): object
    {
        return $this->belongsTo(Affiliate::class);
    }
}
