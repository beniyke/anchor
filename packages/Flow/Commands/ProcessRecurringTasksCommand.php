<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Process Recurring Tasks Command
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

class ProcessRecurringTasksCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('flow:recur')
            ->setDescription('Process and spawn next instances of recurring tasks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Flow: Recurring Tasks');

        try {
            $io->text('Processing recurring tasks...');

            Flow::recurring()->processRecurringTasks();

            $io->success('Successfully processed recurring tasks.');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('Failed to process recurring tasks: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
