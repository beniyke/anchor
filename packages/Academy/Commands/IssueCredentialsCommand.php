<?php

declare(strict_types=1);

namespace Academy\Commands;

use Academy\Services\EnrolmentManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class IssueCredentialsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('academy:credentials:issue')
            ->setDescription('Bulk issue credentials for completed programs')
            ->addOption('program', null, InputOption::VALUE_OPTIONAL, 'Specific program ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $programId = $input->getOption('program');

        $io->title('Issuing Credentials');
        $io->info("Checking for completed enrolments pending credentials...");

        try {
            $service = resolve(EnrolmentManagerService::class);
            $count = $service->bulkIssueCredentials($programId ? (int)$programId : null);

            $io->success("Successfully issued credentials for {$count} enrolments.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Failed to issue credentials: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
