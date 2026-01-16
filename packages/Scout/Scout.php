<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Scout.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout;

use App\Models\User;
use BadMethodCallException;
use Scout\Models\Application;
use Scout\Models\Candidate;
use Scout\Models\Interview;
use Scout\Models\Job;
use Scout\Models\Offer;
use Scout\Models\Stage;
use Scout\Services\Builders\ApplicationBuilder;
use Scout\Services\Builders\CandidateBuilder;
use Scout\Services\Builders\JobBuilder;
use Scout\Services\RecruitmentManagerService;
use Scout\Services\ScoutAnalyticsService;
use Scout\Services\ScoutManagerService;

/**
 * Scout Facade
 *
 * @method static Job         createJob(array $data)
 * @method static Candidate   findOrCreateCandidate(array $data)
 * @method static Application apply(Candidate $candidate, Job $job)
 * @method static void        advance(Application $application, Stage $stage)
 * @method static void        reject(Application $application, string $reason)
 * @method static Interview   scheduleInterview(Application $application, User $interviewer, array $data)
 * @method static Offer       createOffer(Application $application, float $amount, array $data)
 */
class Scout
{
    /**
     * Get the ScoutManagerService instance.
     */
    protected static function manager(): ScoutManagerService
    {
        return resolve(ScoutManagerService::class);
    }

    /**
     * Start building a new job posting.
     */
    public static function job(): JobBuilder
    {
        return new JobBuilder(static::manager());
    }

    /**
     * Start building a new candidate profile.
     */
    public static function candidate(): CandidateBuilder
    {
        return new CandidateBuilder(static::manager());
    }

    /**
     * Start a new job application.
     */
    public static function application(): ApplicationBuilder
    {
        return new ApplicationBuilder(static::manager());
    }

    public static function analytics(): ScoutAnalyticsService
    {
        return resolve(ScoutAnalyticsService::class);
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

        $recruitment = resolve(RecruitmentManagerService::class);
        if (method_exists($recruitment, $method)) {
            return $recruitment->$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on Scout facade.");
    }
}
