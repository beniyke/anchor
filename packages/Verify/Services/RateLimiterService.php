<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rate Limiter Service
 *
 * Tracks and enforces rate limits for OTP operations
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Database\DB;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Verify\Exceptions\RateLimitExceededException;

class RateLimiterService
{
    private const TABLE_NAME = 'verify_attempt';
    private const TYPE_GENERATION = 'generation';
    private const TYPE_VERIFICATION = 'verification';

    public function checkGeneration(string $identifier, int $limit, int $windowMinutes): bool
    {
        return $this->check($identifier, self::TYPE_GENERATION, $limit, $windowMinutes);
    }

    public function checkVerification(string $identifier, int $limit, int $windowMinutes): bool
    {
        return $this->check($identifier, self::TYPE_VERIFICATION, $limit, $windowMinutes);
    }

    public function incrementGeneration(string $identifier): void
    {
        $this->increment($identifier, self::TYPE_GENERATION);
    }

    public function incrementVerification(string $identifier): void
    {
        $this->increment($identifier, self::TYPE_VERIFICATION);
    }

    public function reset(string $identifier, string $type): void
    {
        DB::table(self::TABLE_NAME)
            ->where('identifier', $identifier)
            ->where('attempt_type', $type)
            ->delete();
    }

    public function cleanup(int $daysOld = 7): int
    {
        $cutoff = DateTimeHelper::now()->subDays($daysOld)->toDateTimeString();

        return DB::table(self::TABLE_NAME)
            ->whereLessThan('window_start', $cutoff)
            ->delete();
    }

    private function check(string $identifier, string $type, int $limit, int $windowMinutes): bool
    {
        return DB::transaction(function () use ($identifier, $type, $limit, $windowMinutes) {
            $now = DateTimeHelper::now();
            $windowStart = $now->copy()->subMinutes($windowMinutes);

            $attempt = DB::table(self::TABLE_NAME)
                ->where('identifier', $identifier)
                ->where('attempt_type', $type)
                ->lockForUpdate()
                ->first();

            if (! $attempt) {
                return true;
            }

            $windowStart = DateTimeHelper::parse($attempt->window_start);
            $windowEnd = $windowStart->addMinutes($windowMinutes);

            if ($now->greaterThan($windowEnd)) {
                return true;
            }

            if ((int) $attempt->count >= $limit) {
                throw new RateLimitExceededException($identifier, $type, $limit, $windowMinutes);
            }

            return true;
        });
    }

    private function increment(string $identifier, string $type): void
    {
        DB::transaction(function () use ($identifier, $type) {
            $now = DateTimeHelper::now();

            $attempt = DB::table(self::TABLE_NAME)
                ->where('identifier', $identifier)
                ->where('attempt_type', $type)
                ->lockForUpdate()
                ->first();

            if ($attempt) {
                DB::table(self::TABLE_NAME)
                    ->where('identifier', $identifier)
                    ->where('attempt_type', $type)
                    ->update([
                        'count' => (int) $attempt->count + 1,
                        'updated_at' => $now->toDateTimeString(),
                    ]);
            } else {
                DB::table(self::TABLE_NAME)->insert([
                    'identifier' => $identifier,
                    'refid' => Str::random('secure'),
                    'attempt_type' => $type,
                    'count' => 1,
                    'window_start' => $now->toDateTimeString(),
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString(),
                ]);
            }
        });
    }
}
