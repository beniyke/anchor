<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Academy LMS
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy;

use Academy\Services\AcademyAnalyticsService;
use Academy\Services\AssessmentService;
use Academy\Services\AttendanceService;
use Academy\Services\BadgeService;
use Academy\Services\CertificateService;
use Academy\Services\DiscussionService;
use Academy\Services\EnrolmentManagerService;
use Academy\Services\LandingPageService;
use Academy\Services\LiveSessionService;
use Academy\Services\PaymentManagerService;
use Academy\Services\ProgramManagerService;
use Academy\Services\ProgressTrackingService;
use Academy\Services\RatingService;
use Academy\Services\ReportingService;
use Core\Services\ConfigServiceInterface;
use RuntimeException;

/**
 * Static facade for the Academy LMS package.
 *
 * @method static Builders\ProgramBuilder program()
 * @method static ProgramManagerService   programs()
 * @method static EnrolmentManagerService enrolments()
 * @method static PaymentManagerService   payments()
 * @method static ProgressTrackingService progress()
 * @method static AssessmentService       assessments()
 * @method static CertificateService      certificates()
 * @method static DiscussionService       discussions()
 * @method static LiveSessionService      liveSessions()
 * @method static BadgeService            badges()
 * @method static LandingPageService      landingPages()
 * @method static AcademyAnalyticsService analytics()
 * @method static ReportingService        reports()
 * @method static RatingService           ratings()
 * @method static AttendanceService       attendance()
 * @method static string                  getDefaultCurrency()
 * @method static mixed                   getConfig(?string $key = null, mixed $default = null)
 * @method static array                   searchInstructors(string $query)
 * @method static array                   searchLearners(string $query)
 * @method static Models\AcademyEnrolment enrol(int $userId, int|Models\AcademyProgram $program, ?int $planId = null)
 * @method static void                    gradeQuiz(Models\AcademySubmission $submission)
 * @method static array                   getProgramDetails(int|string $id)
 * @method static array                   getLessonDetails(int|string $id)
 * @method static array                   getEnrolmentDetails(int|string $id)
 */
class Academy
{
    private static ?AcademyManager $instance = null;

    public static function instance(): AcademyManager
    {
        if (self::$instance === null) {
            $config = resolve(ConfigServiceInterface::class);

            if (! $config) {
                throw new RuntimeException("ConfigServiceInterface could not be resolved.");
            }

            self::$instance = new AcademyManager($config);
        }

        return self::$instance;
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        return self::instance()->$method(...$arguments);
    }
}
