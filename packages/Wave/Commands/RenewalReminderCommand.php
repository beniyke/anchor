<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Renewal Reminder Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Wave\Wave;

class RenewalReminderCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('wave:remind')
            ->setDescription('Send renewal reminders for subscriptions ending soon.')
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Days ahead to check for renewals', 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Wave: Renewal Reminders');

        $days = (int) $input->getOption('days');

        try {
            $io->text("Checking for subscriptions renewing in {$days} days...");

            $count = Wave::subscriptions()->sendRenewalReminders($days);

            if ($count > 0) {
                $io->success("Sent {$count} renewal notifications.");
            } else {
                $io->info('No subscriptions require reminders at this time.');
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('Failed to send renewal reminders: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
