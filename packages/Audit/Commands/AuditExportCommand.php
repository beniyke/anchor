<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CLI command to export audit logs.
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

class AuditExportCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('audit:export')
            ->setDescription('Export audit logs to file')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Export format (csv, json)', 'csv')
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Output file path', null)
            ->addOption('event', 'e', InputOption::VALUE_OPTIONAL, 'Filter by event type', null)
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Filter from date (Y-m-d)', null)
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Filter to date (Y-m-d)', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $format = $input->getOption('format');
        $outputPath = $input->getOption('output');

        $filters = [];

        if ($event = $input->getOption('event')) {
            $filters['event'] = $event;
        }

        if ($from = $input->getOption('from')) {
            $filters['from'] = $from;
        }

        if ($to = $input->getOption('to')) {
            $filters['to'] = $to;
        }

        $io->title('Exporting Audit Logs');

        try {
            $content = Audit::export($filters, $format);

            if ($outputPath) {
                file_put_contents($outputPath, $content);
                $io->success("Audit logs exported to: {$outputPath}");
            } else {
                $output->writeln($content);
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to export: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
