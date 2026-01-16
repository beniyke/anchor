<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CLI command to manage feature flags.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Commands;

use Rollout\Models\Feature;
use Rollout\Rollout;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RolloutStatusCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('rollout:status')
            ->setDescription('Show status of all feature flags')
            ->addArgument('feature', InputArgument::OPTIONAL, 'Specific feature slug')
            ->addOption('enable', null, InputOption::VALUE_NONE, 'Enable the feature')
            ->addOption('disable', null, InputOption::VALUE_NONE, 'Disable the feature')
            ->addOption('percentage', 'p', InputOption::VALUE_OPTIONAL, 'Set rollout percentage');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $featureSlug = $input->getArgument('feature');

        // Handle specific feature actions
        if ($featureSlug) {
            if ($input->getOption('enable')) {
                Rollout::enable($featureSlug);
                $io->success("Feature '{$featureSlug}' enabled.");

                return Command::SUCCESS;
            }

            if ($input->getOption('disable')) {
                Rollout::disable($featureSlug);
                $io->success("Feature '{$featureSlug}' disabled.");

                return Command::SUCCESS;
            }

            if ($percentage = $input->getOption('percentage')) {
                Rollout::setPercentage($featureSlug, (int) $percentage);
                $io->success("Feature '{$featureSlug}' set to {$percentage}%.");

                return Command::SUCCESS;
            }
        }

        // Show status
        $io->title('Feature Flag Status');

        $features = Rollout::all();

        if (empty($features)) {
            $io->info('No feature flags defined.');

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($features as $feature) {
            $status = $feature['is_enabled'] ? '<fg=green>ON</>' : '<fg=red>OFF</>';
            $rows[] = [
                $feature['slug'],
                $feature['name'],
                $status,
                $feature['percentage'] . '%',
                $feature['starts_at'] ?? '-',
                $feature['ends_at'] ?? '-',
            ];
        }

        $io->table(
            ['Slug', 'Name', 'Status', 'Percentage', 'Starts At', 'Ends At'],
            $rows
        );

        return Command::SUCCESS;
    }
}
