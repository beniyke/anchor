<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Transaction Query Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Services\Builders;

use Database\Query\Builder;
use Wallet\Enums\TransactionStatus;
use Wallet\Enums\TransactionType;
use Wallet\Models\Transaction;
use Wallet\Models\Wallet;

class TransactionQueryBuilder
{
    private Builder $query;

    public function __construct(
        private readonly int|Wallet $wallet
    ) {
        $walletId = $wallet instanceof Wallet ? $wallet->id : $wallet;
        $this->query = Transaction::query()->where('wallet_id', $walletId);
    }

    public function credit(): self
    {
        $this->query->where('type', TransactionType::CREDIT->value);

        return $this;
    }

    public function debit(): self
    {
        $this->query->where('type', TransactionType::DEBIT->value);

        return $this;
    }

    public function type(string|TransactionType $type): self
    {
        $value = $type instanceof TransactionType ? $type->value : strtoupper($type);
        $this->query->where('type', $value);

        return $this;
    }

    public function status(string|TransactionStatus $status): self
    {
        $value = $status instanceof TransactionStatus ? $status->value : strtoupper($status);
        $this->query->where('status', $value);

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query->limit($limit);

        return $this;
    }

    public function latest(): self
    {
        $this->query->orderBy('created_at', 'DESC');

        return $this;
    }

    public function oldest(): self
    {
        $this->query->orderBy('created_at', 'ASC');

        return $this;
    }

    public function get(): iterable
    {
        return $this->query->get();
    }

    public function first(): ?Transaction
    {
        return $this->query->first();
    }
}
