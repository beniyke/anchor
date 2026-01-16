<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Process Renewals Command
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Commands;

use Helpers\DateTimeHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Wave\Enums\SubscriptionStatus;
use Wave\Models\Subscription;
use Wave\Wave;

class ProcessRenewalsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('wave:renew')
            ->setDescription('Process renewals for subscriptions that have reached their period end.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Wave: Process Renewals');

        try {
            $io->text('Fetching subscriptions overdue for renewal...');

            $subscriptions = Subscription::query()
                ->where(function ($query) {
                    $query->where('status', SubscriptionStatus::ACTIVE->value)
                        ->where('current_period_end', '<=', DateTimeHelper::now());
                })
                ->orWhere(function ($query) {
                    $query->where('status', SubscriptionStatus::TRIALING->value)
                        ->where('trial_ends_at', '<=', DateTimeHelper::now());
                })
                ->whereNull('ends_at')
                ->get();

            $total = count($subscriptions);

            if ($total === 0) {
                $io->info('No subscriptions are due for renewal.');

                return Command::SUCCESS;
            }

            $io->progressStart($total);
            $success = 0;
            $failed = 0;

            foreach ($subscriptions as $subscription) {
                try {
                    Wave::invoices()->createFromSubscription($subscription);
                    $success++;
                } catch (Throwable $e) {
                    $failed++;
                    logger('wave.log')->error("CLI Renewal Failed for Sub #{$subscription->id}: " . $e->getMessage());
                }
                $io->progressAdvance();
            }

            $io->progressFinish();

            if ($success > 0) {
                $io->success("Successfully processed {$success} renewals.");
            }
            if ($failed > 0) {
                $io->warning("Failed to process {$failed} renewals. Check wave.log for details.");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error('A critical error occurred during renewal processing: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
