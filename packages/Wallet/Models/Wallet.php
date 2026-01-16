<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Model
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Database\Relations\MorphTo;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;
use Money\Currency as MoneyCurrency;
use Money\Money;
use Wallet\Enums\Currency;

/**
 * @property int             $id
 * @property int             $owner_id
 * @property string          $owner_type
 * @property string          $refid
 * @property int             $balance
 * @property Currency        $currency
 * @property ?DateTimeHelper $last_transaction_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read BaseModel $owner
 * @property-read ModelCollection $transactions
 */
class Wallet extends BaseModel
{
    use HasRefid;

    protected string $table = 'wallet';

    protected array $fillable = [
        'owner_id',
        'owner_type',
        'refid',
        'balance',
        'currency',
        'last_transaction_at',
    ];

    protected array $casts = [
        'id' => 'integer',
        'balance' => 'int',
        'currency' => Currency::class,
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo('owner', 'owner_type', 'owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    public function getBalanceAsMoney(): Money
    {
        return Money::make($this->balance, MoneyCurrency::of($this->currency instanceof Currency ? $this->currency->value : $this->currency));
    }

    /**
     * Update balance (with locking)
     *
     * @internal Use TransactionManagerService instead
     */
    public function updateBalance(int $amount): void
    {
        $this->balance += $amount;
        $this->last_transaction_at = DateTimeHelper::now()->toDateTimeString();
        $this->save();
    }

    /**
     * Calculate balance from transaction ledger (source of truth)
     */
    public function calculateBalanceFromLedger(): Money
    {
        $balanceInCents = $this->transactions()
            ->where('status', 'COMPLETED')
            ->sum('net_amount');

        return Money::make((int) $balanceInCents, MoneyCurrency::of($this->currency instanceof Currency ? $this->currency->value : $this->currency));
    }
}
