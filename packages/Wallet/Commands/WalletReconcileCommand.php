<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Reconcile Command
 *
 * Reconcile wallet balance with transaction ledger
 * CRITICAL: This command IS allowed in production for maintenance
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Commands;

use Database\DB;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wallet\Models\Wallet;
use Wallet\Services\BalanceManagerService;

class WalletReconcileCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('wallet:reconcile')
            ->setDescription('Reconcile wallet balance with transaction ledger')
            ->setHelp('This command verifies that cached wallet balances match the transaction ledger and fixes any discrepancies.')
            ->addArgument('wallet_id', InputArgument::OPTIONAL, 'The wallet ID (reconciles all wallets if not provided)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $walletId = $input->getArgument('wallet_id');

        if ($walletId) {
            return $this->reconcileWallet($io, (int) $walletId);
        }

        return $this->reconcileAllWallets($io);
    }

    private function reconcileWallet(SymfonyStyle $io, int $walletId): int
    {
        try {
            $balanceManager = new BalanceManagerService();

            $wallet = Wallet::find($walletId);

            if (! $wallet) {
                $io->error("Wallet #{$walletId} not found");

                return Command::FAILURE;
            }

            $io->text("Reconciling wallet #{$walletId}...");

            $isBalanced = DB::transaction(function () use ($balanceManager, $walletId) {
                return $balanceManager->reconcile($walletId);
            });

            if ($isBalanced) {
                $io->success("✓ Wallet #{$walletId}: Balance matches ledger");
            } else {
                $io->warning("⚠ Wallet #{$walletId}: Balance mismatch detected and fixed");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error("Reconciliation failed: {$e->getMessage()}");
            logger('wallet.log')->error('Reconcile command failed', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }

    private function reconcileAllWallets(SymfonyStyle $io): int
    {
        $io->title('Reconciling All Wallets');

        try {
            $balanceManager = new BalanceManagerService();
            $wallets = Wallet::all();
            $total = count($wallets);
            $mismatches = 0;
            $errors = 0;

            $io->progressStart($total);

            foreach ($wallets as $wallet) {
                try {
                    $isBalanced = DB::transaction(function () use ($balanceManager, $wallet) {
                        return $balanceManager->reconcile($wallet->id);
                    });

                    if (! $isBalanced) {
                        $mismatches++;
                        $io->warning("⚠ Wallet #{$wallet->id}: Balance mismatch fixed");
                    }
                } catch (Exception $e) {
                    $errors++;
                    $io->error("✗ Wallet #{$wallet->id}: {$e->getMessage()}");
                }

                $io->progressAdvance();
            }

            $io->progressFinish();

            $io->newLine();
            $io->success("Reconciliation complete:");
            $io->listing([
                "Total wallets: {$total}",
                "Mismatches fixed: {$mismatches}",
                "Errors: {$errors}",
                "Balanced: " . ($total - $mismatches - $errors),
            ]);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error("Bulk reconciliation failed: {$e->getMessage()}");
            logger('wallet.log')->error('Bulk reconcile failed', [
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
