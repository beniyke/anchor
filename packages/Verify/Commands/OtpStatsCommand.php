<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * OTP Statistics Command
 *
 * Displays OTP generation and verification statistics
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Commands;

use Exception;
use Helpers\Log;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Verify\Services\VerifyAnalyticsService;

class OtpStatsCommand extends Command
{
    public function __construct(
        private readonly VerifyAnalyticsService $analytics
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('verify:stats')
            ->setDescription('Display OTP verification statistics')
            ->setHelp('This command shows statistics about OTP generation, verification, and usage patterns.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('OTP Verification Statistics');

        try {
            $stats = $this->analytics->getOverviewStats();

            // Display statistics
            $io->section('Overview');
            $io->table(
                ['Metric', 'Value'],
                [
                    ['Active Codes', $stats['active_count']],
                    ['Generated Today', $stats['today_count']],
                    ['Verified Today', $stats['verified_today_count']],
                    ['Success Rate Today', "{$stats['success_rate_today']}%"],
                    ['Expired Codes', $stats['expired_count']],
                    ['Rate Limit Violations', $stats['rate_limit_violations']],
                ]
            );

            if (count($stats['channel_stats']) > 0) {
                $io->section('Channel Usage (Today)');
                $channelData = [];
                foreach ($stats['channel_stats'] as $stat) {
                    $channelData[] = [$stat['channel'], $stat['count']];
                }
                $io->table(['Channel', 'Count'], $channelData);
            }

            if ($stats['expired_count'] > 0) {
                $io->note("Run 'php dock verify:cleanup' to remove {$stats['expired_count']} expired code(s)");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to retrieve statistics: ' . $e->getMessage());
            Log::channel('verify')->error('Stats command failed', [
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
