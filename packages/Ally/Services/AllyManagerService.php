<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * AllyManagerService handles reseller registration, tier management,
 * and distribution credit provisioning.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Ally\Services;

use Ally\Events\LowCreditEvent;
use Ally\Exceptions\AllyException;
use Ally\Exceptions\InsufficientCreditsException;
use Ally\Models\Reseller;
use Ally\Services\Builders\AllyBuilder;
use Core\Event;
use Database\DB;
use Database\Exceptions\ValidationException;
use Helpers\Validation\Validator;
use Money\Money;
use RuntimeException;
use Wallet\Enums\Currency;
use Wallet\Services\WalletManagerService;

class AllyManagerService
{
    public function __construct(
        private readonly WalletManagerService $walletManager
    ) {
    }

    public function make(): AllyBuilder
    {
        return new AllyBuilder();
    }

    /** @throws ValidationException */
    public function create(array $data): Reseller
    {
        $reseller = Reseller::create($data);

        // Ensure reseller has a wallet for credits
        $this->ensureWallet($reseller);

        return $reseller;
    }

    protected function ensureWallet(Reseller $reseller): void
    {
        if (!$this->getResellerWallet($reseller)) {
            $this->walletManager->createWallet()
                ->owner($reseller->id, Reseller::class)
                ->currency(Currency::USD)
                ->create();
        }
    }

    protected function getResellerWallet(Reseller $reseller): ?object
    {
        return $this->walletManager->findByOwner($reseller->id, Reseller::class, 'USD');
    }

    public function findByUser(int|string $userId): ?Reseller
    {
        return Reseller::query()->where('user_id', $userId)->first();
    }

    public function findByRefid(string $refid): ?Reseller
    {
        return Reseller::query()->where('refid', $refid)->first();
    }

    public function addCredits(Reseller|int|string $reseller, int $amount): void
    {
        if (! $reseller instanceof Reseller) {
            $reseller = Reseller::find($reseller);
        }

        if (!$reseller) {
            throw new RuntimeException("Reseller not found.");
        }

        $wallet = $this->getResellerWallet($reseller);
        if (!$wallet) {
            throw new RuntimeException("Reseller wallet not found.");
        }

        $this->walletManager->credit($wallet->id, Money::make($amount, 'USD'), [
            'description' => 'Distribution credits added',
        ]);
    }

    public function provision(Reseller|int|string $reseller, int $cost, string $action = 'Service Provisioning'): bool
    {
        $validator = (new Validator())->rules([
            'cost' => ['required' => true, 'type' => 'integer', 'limit' => '1'],
        ])->validate(['cost' => $cost]);

        if ($validator->has_error()) {
            throw new ValidationException("Provision validation failed.", $validator->errors());
        }

        if (! $reseller instanceof Reseller) {
            $reseller = Reseller::find($reseller);
        }

        if (! $reseller) {
            throw new AllyException("Reseller not found.");
        }

        return DB::transaction(function () use ($reseller, $cost, $action) {
            $wallet = $this->getResellerWallet($reseller);

            if (!$wallet || $this->walletManager->getBalance($wallet->id)->getAmount() < $cost) {
                throw InsufficientCreditsException::create($wallet ? $this->walletManager->getBalance($wallet->id)->getAmount() : 0, $cost);
            }

            $this->walletManager->debit($wallet->id, Money::make($cost, 'USD'), [
                'description' => $action,
            ]);

            // Dispatch event and check for low credit threshold
            $threshold = config('ally.low_credit_threshold', 500);
            $newBalance = $this->walletManager->getBalance($wallet->id)->getAmount();

            if ($newBalance <= $threshold) {
                Event::dispatch(new LowCreditEvent($reseller, $newBalance, $threshold));
            }

            return true;
        });
    }
}
