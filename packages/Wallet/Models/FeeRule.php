<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fee Rule
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Models;

use Database\BaseModel;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $transaction_type
 * @property string          $payment_processor
 * @property string          $fee_type
 * @property int             $fixed_amount
 * @property float           $percentage
 * @property int             $min_fee
 * @property int             $max_fee
 * @property int             $min_transaction_amount
 * @property int             $max_transaction_amount
 * @property string          $currency
 * @property bool            $is_active
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class FeeRule extends BaseModel
{
    protected string $table = 'wallet_fee_rule';

    protected array $fillable = [
        'name',
        'transaction_type',
        'payment_processor',
        'fee_type',
        'fixed_amount',
        'percentage',
        'min_fee',
        'max_fee',
        'min_transaction_amount',
        'max_transaction_amount',
        'currency',
        'is_active',
    ];

    protected array $casts = [
        'fixed_amount' => 'integer',
        'percentage' => 'float',
        'min_fee' => 'integer',
        'max_fee' => 'integer',
        'min_transaction_amount' => 'integer',
        'max_transaction_amount' => 'integer',
        'is_active' => 'boolean',
    ];
}
