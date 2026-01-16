<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Discount Model for tracking applied coupons.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Models;

use Database\BaseModel;
use Database\Traits\HasRefid;

class Discount extends BaseModel
{
    use HasRefid;

    protected string $table = 'wave_discount';

    protected array $fillable = [
        'refid',
        'owner_id',
        'owner_type',
        'subscription_id',
        'invoice_id',
        'coupon_id',
        'amount_saved'
    ];

    protected array $casts = [
        'id' => 'integer',
        'amount_saved' => 'integer'
    ];

    public function coupon(): object
    {
        return $this->belongsTo(Coupon::class);
    }
}
