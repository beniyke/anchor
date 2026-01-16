<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CLI command to clean up old audit logs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Audit\Commands;

use Audit\Audit;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AuditCleanupCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('audit:cleanup')
            ->setDescription('Clean up old audit logs')
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Days to retain', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $days = $input->getOption('days');
        $days = $days !== null ? (int) $days : null;

        $io->title('Cleaning Up Audit Logs');

        try {
            $count = Audit::cleanup($days);

            if ($count > 0) {
                $io->success("Deleted {$count} old audit log entries.");
            } else {
                $io->info('No audit logs to clean up.');
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to clean up: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
