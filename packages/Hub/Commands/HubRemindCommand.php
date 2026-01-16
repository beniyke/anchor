<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Hub Remind Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Commands;

use Core\Event;
use Hub\Events\ReminderDueEvent;
use Hub\Models\Reminder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class HubRemindCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('hub:remind')
            ->setDescription('Process due reminders and dispatch notifications.')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Maximum reminders to process', 100);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Hub: Process Due Reminders');

        try {
            $limit = (int) ($input->getOption('limit') ?: 100);
            $io->text("Processing up to {$limit} due reminders...");

            $reminders = Reminder::due()->limit($limit)->get();
            $total = count($reminders);

            if ($total === 0) {
                $io->info('No due reminders found.');

                return Command::SUCCESS;
            }

            $io->progressStart($total);
            $success = 0;
            $failed = 0;

            foreach ($reminders as $reminder) {
                try {
                    Event::dispatch(new ReminderDueEvent($reminder, $reminder->user_id));

                    if ($reminder->repeats()) {
                        $reminder->rescheduleNext();
                    } else {
                        $reminder->complete();
                    }

                    $success++;
                } catch (Throwable $e) {
                    $failed++;
                    $io->error("Failed to process reminder #{$reminder->refid}: " . $e->getMessage());
                    logger('hub.log')->error("Reminder failed #{$reminder->refid}: " . $e->getMessage());
                }
                $io->progressAdvance();
            }

            $io->progressFinish();

            if ($success > 0) {
                $io->success("Successfully processed {$success} reminder(s).");
            }
            if ($failed > 0) {
                $io->warning("Failed to process {$failed} reminder(s). Check hub.log for details.");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
