<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Model
 *
 * Immutable ledger record - never updated or deleted
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Wallet\Enums\TransactionStatus;
use Wallet\Enums\TransactionType;

/**
 * @property int               $id
 * @property int               $wallet_id
 * @property TransactionType   $type
 * @property string            $refid
 * @property int               $amount
 * @property int               $fee
 * @property int               $net_amount
 * @property int               $balance_before
 * @property int               $balance_after
 * @property ?int              $parent_transaction_id
 * @property ?string           $reference_id
 * @property ?string           $idempotency_key
 * @property string            $payment_processor
 * @property ?string           $processor_transaction_id
 * @property int               $processor_fee
 * @property ?string           $description
 * @property ?array            $metadata
 * @property TransactionStatus $status
 * @property ?DateTimeHelper   $created_at
 * @property ?DateTimeHelper   $completed_at
 * @property-read Wallet $wallet
 * @property-read ?Transaction $parent
 * @property-read ModelCollection $children
 */
class Transaction extends BaseModel
{
    use HasRefid;

    protected string $table = 'wallet_transaction';

    public bool $timestamps = false;

    protected array $fillable = [
        'wallet_id',
        'type',
        'refid',
        'amount',
        'fee',
        'net_amount',
        'balance_before',
        'balance_after',
        'reference_id',
        'idempotency_key',
        'parent_transaction_id',
        'payment_processor',
        'processor_transaction_id',
        'processor_fee',
        'description',
        'metadata',
        'status',
        'created_at',
        'completed_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'wallet_id' => 'int',
        'type' => TransactionType::class,
        'amount' => 'int',
        'fee' => 'int',
        'net_amount' => 'int',
        'balance_before' => 'int',
        'balance_after' => 'int',
        'parent_transaction_id' => 'int',
        'processor_fee' => 'int',
        'metadata' => 'array',
        'status' => TransactionStatus::class,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_transaction_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_transaction_id');
    }
}
