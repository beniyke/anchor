<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Stack Maintenance Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack\Commands;

use Stack\Services\StackManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class StackCleanupCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('stack:cleanup')
            ->setDescription('Cleanup old form submissions and logs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Stack: Cleanup');

        try {
            $io->text('Scanning for old submissions via StackManagerService...');

            $manager = resolve(StackManagerService::class);
            $count = $manager->cleanupSubmissions();

            $io->success("Stack cleanup completed. Scanned {$count} submissions.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred during Stack cleanup: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
