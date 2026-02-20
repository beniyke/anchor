<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CLI command to prune old activity logs.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Commands;

use Activity\Services\ActivityManagerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PruneActivityCommand extends Command
{
    protected static $defaultName = 'activity:prune';

    public function __construct(
        private readonly ActivityManagerService $activityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('activity:prune')
            ->setDescription('Prune old activity logs')
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_REQUIRED,
                'How many days of logs to keep?',
                30
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $days = (int) $input->getOption('days');

        $io->comment("Pruning activity logs older than {$days} days...");

        $count = $this->activityManager->prune($days);

        $io->success("Successfully pruned {$count} activity logs.");

        return Command::SUCCESS;
    }
}
