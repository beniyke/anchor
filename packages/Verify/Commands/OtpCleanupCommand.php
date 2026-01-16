<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Cleanup Command
 *
 * Removes expired OTP codes and old attempt records
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Commands;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Verify\Services\OtpStorageService;
use Verify\Services\RateLimiterService;

class OtpCleanupCommand extends Command
{
    public function __construct(
        private readonly OtpStorageService $storage,
        private readonly RateLimiterService $rateLimiter
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('verify:cleanup')
            ->setDescription('Clean up expired OTP codes and old attempt records')
            ->setHelp('This command removes expired OTP codes and old rate limit attempt records from the database.')
            ->addOption('days', 'd', InputOption::VALUE_OPTIONAL, 'Number of days to keep attempt records (default: 7)', 7);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('OTP Cleanup');

        try {
            // Clean up expired OTP codes
            $io->section('Cleaning up expired OTP codes...');
            $deletedCodes = $this->storage->cleanup();
            $io->success("Deleted {$deletedCodes} expired OTP code(s)");

            // Clean up old attempt records
            $days = (int) $input->getOption('days');
            $io->section("Cleaning up attempt records older than {$days} days...");
            $deletedAttempts = $this->rateLimiter->cleanup($days);
            $io->success("Deleted {$deletedAttempts} old attempt record(s)");

            $io->note('Cleanup completed successfully');

            logger('verify.log')->info('OTP cleanup completed', [
                'deleted_codes' => $deletedCodes,
                'deleted_attempts' => $deletedAttempts,
                'retention_days' => $days,
            ]);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Cleanup failed: ' . $e->getMessage());
            logger('verify.log')->error('OTP cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
