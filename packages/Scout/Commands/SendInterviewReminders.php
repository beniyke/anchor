<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Commands;

use Scout\Scout;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendInterviewReminders extends Command
{
    protected function configure(): void
    {
        $this->setName('scout:reminders')
            ->setDescription('Send reminders for upcoming interviews');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Scout: Interview Reminders');

        $io->text('Finding upcoming interviews...');

        $count = Scout::sendReminders();

        $io->success("Sent {$count} interview reminders.");

        return Command::SUCCESS;
    }
}
