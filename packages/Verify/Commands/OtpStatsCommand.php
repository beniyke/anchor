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

use Database\DB;
use Exception;
use Helpers\DateTimeHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OtpStatsCommand extends Command
{
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
            // Active codes (not expired, not verified)
            $activeCount = DB::table('verify_otp_code')
                ->whereAfter('expires_at', DateTimeHelper::now()->toDateTimeString())
                ->whereNull('verified_at')
                ->count();

            // Total codes generated today
            $todayStart = DateTimeHelper::now()->startOfDay()->toDateTimeString();
            $todayCount = DB::table('verify_otp_code')
                ->whereOnOrAfter('created_at', $todayStart)
                ->count();

            // Total verified codes today
            $verifiedTodayCount = DB::table('verify_otp_code')
                ->whereOnOrAfter('created_at', $todayStart)
                ->whereNotNull('verified_at')
                ->count();

            // Verification success rate today
            $successRate = $todayCount > 0
                ? round(($verifiedTodayCount / $todayCount) * 100, 2)
                : 0;

            // Expired codes (cleanup candidates)
            $expiredCount = DB::table('verify_otp_code')
                ->whereBefore('expires_at', DateTimeHelper::now()->toDateTimeString())
                ->count();

            // Rate limit violations (high attempt counts)
            $rateLimitViolations = DB::table('verify_attempt')
                ->whereGreaterThanOrEqual('count', 5)
                ->count();

            // Channel breakdown for today
            $channelStats = DB::table('verify_otp_code')
                ->whereOnOrAfter('created_at', $todayStart)
                ->select(DB::raw('channel, COUNT(*) as count'))
                ->groupBy('channel')
                ->get();

            // Display statistics
            $io->section('Overview');
            $io->table(
                ['Metric', 'Value'],
                [
                    ['Active Codes', $activeCount],
                    ['Generated Today', $todayCount],
                    ['Verified Today', $verifiedTodayCount],
                    ['Success Rate Today', "{$successRate}%"],
                    ['Expired Codes', $expiredCount],
                    ['Rate Limit Violations', $rateLimitViolations],
                ]
            );

            if (count($channelStats) > 0) {
                $io->section('Channel Usage (Today)');
                $channelData = [];
                foreach ($channelStats as $stat) {
                    $channelData[] = [$stat['channel'], $stat['count']];
                }
                $io->table(['Channel', 'Count'], $channelData);
            }

            if ($expiredCount > 0) {
                $io->note("Run 'php dock verify:cleanup' to remove {$expiredCount} expired code(s)");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Failed to retrieve statistics: ' . $e->getMessage());
            logger('verify.log')->error('Stats command failed', [
                'error' => $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
