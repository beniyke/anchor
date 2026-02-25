<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Academy Manager — Core orchestrator for the Academy LMS package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy;

use Academy\Builders\ProgramBuilder;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyProgram;
use Academy\Models\AcademySubmission;
use Academy\Services\AcademyAnalyticsService;
use Academy\Services\AssessmentService;
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

class AcademyManager
{
    protected array $config;

    public function __construct(ConfigServiceInterface $config)
    {
        $this->config = $config->get('academy') ?? [];
    }

    public function program(): ProgramBuilder
    {
        return new ProgramBuilder($this->programs());
    }

    public function programs(): ProgramManagerService
    {
        return resolve(ProgramManagerService::class);
    }

    public function enrolments(): EnrolmentManagerService
    {
        return resolve(EnrolmentManagerService::class);
    }

    public function payments(): PaymentManagerService
    {
        return resolve(PaymentManagerService::class);
    }

    public function searchInstructors(string $query): array
    {
        return $this->programs()->searchInstructors($query);
    }

    public function searchLearners(string $query): array
    {
        return $this->enrolments()->searchLearners($query);
    }

    public function progress(): ProgressTrackingService
    {
        return resolve(ProgressTrackingService::class);
    }

    public function assessments(): AssessmentService
    {
        return resolve(AssessmentService::class);
    }

    public function certificates(): CertificateService
    {
        return resolve(CertificateService::class);
    }

    public function discussions(): DiscussionService
    {
        return resolve(DiscussionService::class);
    }

    public function liveSessions(): LiveSessionService
    {
        return resolve(LiveSessionService::class);
    }

    public function badges(): BadgeService
    {
        return resolve(BadgeService::class);
    }

    public function landingPages(): LandingPageService
    {
        return resolve(LandingPageService::class);
    }

    public function analytics(): AcademyAnalyticsService
    {
        return resolve(AcademyAnalyticsService::class);
    }

    public function reports(): ReportingService
    {
        return resolve(ReportingService::class);
    }

    public function ratings(): RatingService
    {
        return resolve(RatingService::class);
    }

    public function enrol(int $userId, int|AcademyProgram $program, ?int $planId = null): AcademyEnrolment
    {
        return $this->enrolments()->enrol($userId, $program, $planId);
    }

    public function gradeQuiz(AcademySubmission $submission): void
    {
        $this->assessments()->autoGrade($submission);
    }

    public function getDefaultCurrency(): string
    {
        return $this->config['currency'] ?? 'USD';
    }

    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }

    public function getProgramDetails(int|string $id): array
    {
        return $this->programs()->getProgramDetails($id);
    }

    public function getLessonDetails(int|string $id): array
    {
        return $this->programs()->getLessonDetails($id);
    }

    public function getEnrolmentDetails(int|string $id): array
    {
        return $this->enrolments()->getEnrolmentDetails($id);
    }
}
