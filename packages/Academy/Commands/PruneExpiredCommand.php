<?php

declare(strict_types=1);

namespace Academy\Commands;

use Academy\Services\EnrolmentManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class PruneExpiredCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('academy:prune:expired')
            ->setDescription('Prune expired enrolments and waitlists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Pruning Expired Records');
        $io->info("Pruning expired enrolments and waitlists...");

        try {
            $service = resolve(EnrolmentManagerService::class);
            $enrolments = $service->pruneExpiredEnrolments();
            $waitlists = $service->pruneExpiredWaitlists();

            $io->success("Pruned {$enrolments} enrolments and {$waitlists} waitlist records.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Pruning failed: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
