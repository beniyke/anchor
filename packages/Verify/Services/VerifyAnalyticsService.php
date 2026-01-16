<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Verify Analytics Service
 *
 * Provides metrics on OTP generation, verification success, and failures.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Database\DB;

class VerifyAnalyticsService
{
    private const CODE_TABLE = 'verify_otp_code';
    private const ATTEMPT_TABLE = 'verify_attempt';

    public function getSuccessMetrics(?string $from = null, ?string $to = null): array
    {
        $query = DB::table(self::CODE_TABLE);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as total_generated'),
            DB::raw('COUNT(verified_at) as total_verified'),
            DB::raw('SUM(CASE WHEN expires_at < NOW() AND verified_at IS NULL THEN 1 ELSE 0 END) as total_expired')
        )->first();

        $totalGenerated = (int) $stats->total_generated;
        $totalVerified = (int) $stats->total_verified;
        $totalExpired = (int) $stats->total_expired;
        $totalFailed = $totalGenerated - $totalVerified;

        return [
            'total_generated' => $totalGenerated,
            'total_verified' => $totalVerified,
            'total_expired' => $totalExpired,
            'total_failed' => $totalFailed,
            'success_rate' => $totalGenerated > 0 ? round(($totalVerified / $totalGenerated) * 100, 2) : 0,
        ];
    }

    public function getDailyVolume(string $from, string $to): array
    {
        $trends = DB::table(self::CODE_TABLE)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as generated'),
                DB::raw('COUNT(verified_at) as verified')
            )
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return array_map(function ($row) {
            return [
                'date' => $row->date,
                'generated' => (int) $row->generated,
                'verified' => (int) $row->verified,
            ];
        }, $trends);
    }

    /**
     * Get success rates by channel (e.g., email, sms).
     */
    public function getChannelStats(): array
    {
        $stats = DB::table(self::CODE_TABLE)
            ->select(
                'channel',
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(verified_at) as verified')
            )
            ->groupBy('channel')
            ->get();

        return array_map(function ($row) {
            $total = (int) $row->total;
            $verified = (int) $row->verified;

            return [
                'channel' => $row->channel,
                'total' => $total,
                'verified' => $verified,
                'success_rate' => $total > 0 ? round(($verified / $total) * 100, 2) : 0,
            ];
        }, $stats);
    }

    public function getRateLimitStats(): array
    {
        $stats = DB::table(self::ATTEMPT_TABLE)
            ->select(
                'attempt_type',
                DB::raw('SUM(count) as total_attempts'),
                DB::raw('COUNT(*) as unique_identifiers')
            )
            ->groupBy('attempt_type')
            ->get();

        $result = [];
        foreach ($stats as $item) {
            $result[$item->attempt_type] = [
                'total_attempts' => (int) $item->total_attempts,
                'unique_identifiers' => (int) $item->unique_identifiers,
            ];
        }

        return $result;
    }
}
