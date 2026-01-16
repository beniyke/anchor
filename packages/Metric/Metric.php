<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Metric.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric;

use App\Models\User;
use BadMethodCallException;
use DateTimeInterface;
use Metric\Models\Feedback;
use Metric\Models\KeyResult;
use Metric\Models\Kpi;
use Metric\Models\KpiValue;
use Metric\Models\Recognition;
use Metric\Models\Review;
use Metric\Models\ReviewCycle;
use Metric\Services\Builders\GoalBuilderService;
use Metric\Services\Builders\KpiBuilderService;
use Metric\Services\MetricAnalyticsService;
use Metric\Services\MetricManagerService;
use Metric\Services\PerformanceManagerService;

/**
 * Metric Facade
 *
 * @method static void        updateKeyResult(KeyResult $keyResult, float $value)
 * @method static KpiValue    recordKpi(Kpi $kpi, float $value, ?User $user = null, array $meta = [])
 * @method static Kpi         findOrCreateKpi(string $name, string $unit = 'number')
 * @method static ReviewCycle startCycle(string $name, DateTimeInterface $start, DateTimeInterface $end)
 * @method static Feedback    giveFeedback(User $author, User $recipient, string $content, ?Review $review = null)
 * @method static Recognition recognize(User $sender, User $receiver, string $awardType, string $message)
 */
class Metric
{
    protected static function manager(): MetricManagerService
    {
        return resolve(MetricManagerService::class);
    }

    public static function goal(): GoalBuilderService
    {
        return new GoalBuilderService();
    }

    public static function kpi(): KpiBuilderService
    {
        return new KpiBuilderService();
    }

    public static function analytics(): MetricAnalyticsService
    {
        return resolve(MetricAnalyticsService::class);
    }

    public static function performance(): PerformanceManagerService
    {
        return resolve(PerformanceManagerService::class);
    }

    /**
     * Delegate static calls to the appropriate manager.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $manager = static::manager();
        if (method_exists($manager, $method)) {
            return $manager->$method(...$arguments);
        }

        $performance = static::performance();
        if (method_exists($performance, $method)) {
            return $performance->$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on Metric facade.");
    }
}
