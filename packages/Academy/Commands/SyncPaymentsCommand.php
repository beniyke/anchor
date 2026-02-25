<?php

declare(strict_types=1);

namespace Academy\Commands;

use Academy\Services\PaymentManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class SyncPaymentsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('academy:payments:sync')
            ->setDescription('Synchronize Academy payments from external providers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Syncing Payments');
        $io->info("Starting payment synchronization...");

        try {
            $service = resolve(PaymentManagerService::class);
            $count = $service->syncExternalPayments();

            $io->success("Successfully synchronized {$count} payments.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Payment synchronization failed: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
