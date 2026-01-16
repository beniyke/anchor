<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Command to clean up old Watcher entries.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Cli\Commands\Watcher;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Watcher\Retention\RetentionPolicy;

#[AsCommand(
    name: 'watcher:cleanup',
    description: 'Clean up old Watcher entries based on retention policy'
)]
class RetentionCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_OPTIONAL,
            'Specific event type to clean up (request, query, exception, job, log, cache, mail)'
        );

        $this->addOption(
            'dry-run',
            'd',
            InputOption::VALUE_NONE,
            'Show what would be deleted without actually deleting'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $policy = resolve(RetentionPolicy::class);

        $type = $input->getOption('type');
        $dryRun = $input->getOption('dry-run');

        $io->title('Watcher Retention Cleanup');

        if ($dryRun) {
            $io->note('DRY RUN MODE - No data will be deleted');
            $stats = $policy->getStats();

            $rows = [];
            foreach ($stats as $eventType => $data) {
                $rows[] = [
                    $eventType,
                    $data['retention_days'] . ' days',
                    number_format($data['total_entries']),
                    number_format($data['eligible_for_cleanup']),
                ];
            }

            $io->table(
                ['Type', 'Retention', 'Total Entries', 'Eligible for Cleanup'],
                $rows
            );

            return Command::SUCCESS;
        }

        if ($type) {
            $io->text("Cleaning up <info>{$type}</info> entries...");
            $deleted = $policy->cleanupType($type);
            $io->success("Deleted {$deleted} {$type} entries");
        } else {
            $io->text('Cleaning up all event types...');
            $results = $policy->cleanup();

            if (empty($results)) {
                $io->info('No entries eligible for cleanup');
            } else {
                foreach ($results as $eventType => $count) {
                    $io->writeln("  • <info>{$eventType}</info>: {$count} entries deleted");
                }
                $total = array_sum($results);
                $io->success("Total: {$total} entries deleted");
            }
        }

        return Command::SUCCESS;
    }
}
