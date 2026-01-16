<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Slot Remind Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Commands;

use Core\Event;
use Slot\Events\BookingReminderEvent;
use Slot\SlotManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class SlotRemindCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('slot:remind')
            ->setDescription('Dispatch reminders for upcoming bookings.')
            ->addOption('minutes', 'm', InputOption::VALUE_OPTIONAL, 'The time window to check in minutes', 30);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Slot: Dispatch Reminders');

        try {
            $minutes = (int) ($input->getOption('minutes') ?: 30);
            $io->text("Checking for bookings within the next {$minutes} minutes...");

            /** @var SlotManager $slot */
            $slot = resolve(SlotManager::class);
            $bookings = $slot->getUpcomingBookings($minutes);

            $total = count($bookings);

            if ($total === 0) {
                $io->info('No upcoming bookings found.');

                return Command::SUCCESS;
            }

            $io->progressStart($total);
            $success = 0;
            $failed = 0;

            foreach ($bookings as $booking) {
                try {
                    Event::dispatch(new BookingReminderEvent($booking));

                    $success++;
                } catch (Throwable $e) {
                    $failed++;
                    $io->error("Failed to process booking #{$booking->id}: " . $e->getMessage());
                    logger('slot.log')->error("Reminder failed for Booking #{$booking->id}: " . $e->getMessage());
                }
                $io->progressAdvance();
            }

            $io->progressFinish();

            if ($success > 0) {
                $io->success("Successfully dispatched {$success} reminders.");
            }
            if ($failed > 0) {
                $io->warning("Failed to dispatch {$failed} reminders. Check slot.log for details.");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
