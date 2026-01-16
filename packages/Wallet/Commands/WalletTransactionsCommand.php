<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Transactions Command
 *
 * Display wallet transaction history (Development only)
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Commands;

use Core\Support\Environment;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Wallet\Services\BalanceManagerService;
use Wallet\Services\FeeCalculatorService;
use Wallet\Services\TransactionManagerService;
use Wallet\Services\WalletManagerService;

class WalletTransactionsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('wallet:transaction')
            ->setDescription('Display wallet transaction history')
            ->setHelp('This command displays the transaction history for a wallet.')
            ->addArgument('wallet_id', InputArgument::REQUIRED, 'The wallet ID')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of transactions to display', 20)
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Filter by transaction type (CREDIT, DEBIT, TRANSFER_IN, TRANSFER_OUT, REFUND)')
            ->addOption('status', 's', InputOption::VALUE_OPTIONAL, 'Filter by status (PENDING, COMPLETED, FAILED)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->isProductionEnvironment()) {
            $io->error('This command is disabled in production for security reasons.');

            return Command::FAILURE;
        }

        $walletId = (int) $input->getArgument('wallet_id');
        $limit = (int) $input->getOption('limit');
        $type = $input->getOption('type');
        $status = $input->getOption('status');

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

            $filters = ['limit' => $limit];
            if ($type) {
                $filters['type'] = strtoupper($type);
            }
            if ($status) {
                $filters['status'] = strtoupper($status);
            }

            $transactions = $transactionManager->getWalletTransactions($walletId, $filters);

            $io->title("Transactions for Wallet #{$walletId}");

            if (count($transactions) === 0) {
                $io->note('No transactions found.');
            } else {
                $rows = [];
                foreach ($transactions as $tx) {
                    $rows[] = [
                        $tx->id,
                        $tx->type,
                        number_format($tx->amount / 100, 2),
                        number_format($tx->fee / 100, 2),
                        number_format($tx->net_amount / 100, 2),
                        number_format($tx->balance_after / 100, 2),
                        $tx->status,
                        $tx->created_at,
                    ];
                }

                $io->table(
                    ['ID', 'Type', 'Amount', 'Fee', 'Net', 'Balance After', 'Status', 'Created'],
                    $rows
                );
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to retrieve transactions: ' . $e->getMessage());
            logger('wallet.log')->error('Transactions command failed', [
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
