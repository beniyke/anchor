<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Balance Command
 *
 * Display wallet balance and statistics (Development only)
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Commands;

use Core\Support\Environment;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wallet\Services\BalanceManagerService;
use Wallet\Services\FeeCalculatorService;
use Wallet\Services\TransactionManagerService;
use Wallet\Services\WalletManagerService;

class WalletBalanceCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('wallet:balance')
            ->setDescription('Display wallet balance and statistics')
            ->setHelp('This command displays the current balance and statistics for a wallet.')
            ->addArgument('wallet_id', InputArgument::REQUIRED, 'The wallet ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->isProductionEnvironment()) {
            $io->error('This command is disabled in production for security reasons.');

            return Command::FAILURE;
        }

        $walletId = (int) $input->getArgument('wallet_id');

        try {
            $feeCalculator = new FeeCalculatorService();
            $balanceManager = new BalanceManagerService();
            $transactionManager = new TransactionManagerService($balanceManager, $feeCalculator);
            $walletManager = new WalletManagerService($transactionManager, $balanceManager, $feeCalculator);

            $wallet = $walletManager->find($walletId);

            if (! $wallet) {
                $io->error("Wallet #{$walletId} not found");

                return Command::FAILURE;
            }

            $balance = $walletManager->getBalance($walletId);

            $io->title("Wallet #{$wallet->id}");
            $io->table(
                ['Property', 'Value'],
                [
                    ['Owner Type', $wallet->owner_type],
                    ['Owner ID', $wallet->owner_id],
                    ['Currency', $wallet->currency],
                    ['Balance', $balance->format()],
                    ['Balance (smallest unit)', $balance->getAmount()],
                    ['Last Transaction', $wallet->last_transaction_at ?? 'Never'],
                    ['Created', $wallet->created_at],
                ]
            );

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to retrieve wallet balance: ' . $e->getMessage());
            logger('wallet')->error('Balance command failed', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }

    private function isProductionEnvironment(): bool
    {
        return Environment::isProduction();
    }
}
