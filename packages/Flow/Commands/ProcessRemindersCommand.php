<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Process Reminders Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Commands;

use Flow\Flow;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class ProcessRemindersCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('flow:remind')
            ->setDescription('Process and send all due task reminders.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Flow: Task Reminders');

        try {
            $io->text('Processing task reminders...');

            $count = Flow::reminders()->processReminders();

            if ($count > 0) {
                $io->success("Sent {$count} reminders.");
            } else {
                $io->info('No reminders due at this time.');
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('Failed to process reminders: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
