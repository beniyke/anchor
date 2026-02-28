<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Academy package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Providers;

use Academy\Events\AssessmentGradedEvent;
use Academy\Events\AssessmentSubmittedEvent;
use Academy\Events\BadgeAwardedEvent;
use Academy\Events\DiscussionRepliedEvent;
use Academy\Events\EnrolmentCreatedEvent;
use Academy\Events\InstalmentOverdueEvent;
use Academy\Events\LessonCompletedEvent;
use Academy\Events\LiveSessionStartingEvent;
use Academy\Events\ModuleCompletedEvent;
use Academy\Events\PaymentReceivedEvent;
use Academy\Events\PaymentSuccessfulEvent;
use Academy\Events\ProgramCompletedEvent;
use Academy\Listeners\AwardBadgeListener;
use Academy\Listeners\AwardLearningRewardListener;
use Academy\Listeners\IssueCertificateListener;
use Academy\Listeners\LogActivityListener;
use Academy\Listeners\ProcessPaymentSuccessListener;
use Academy\Listeners\SendAssessmentGradedListener;
use Academy\Listeners\SendEnrolmentNotificationListener;
use Academy\Listeners\SendInstalmentReminderListener;
use Academy\Listeners\SendLiveSessionReminderListener;
use Academy\Listeners\SendPaymentReceiptListener;
use Academy\Listeners\SendProgramCompletedNotificationListener;
use Academy\Models\AcademyEnrolment;
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
use App\Models\User;
use Core\Event;
use Core\Services\ServiceProvider;
use Helpers\File\Paths;

class AcademyServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        // Register Managers & Services as Singletons
        $this->container->singleton(ProgramManagerService::class);
        $this->container->singleton(EnrolmentManagerService::class);
        $this->container->singleton(PaymentManagerService::class);
        $this->container->singleton(ProgressTrackingService::class);
        $this->container->singleton(AssessmentService::class);
        $this->container->singleton(CertificateService::class);
        $this->container->singleton(DiscussionService::class);
        $this->container->singleton(LiveSessionService::class);
        $this->container->singleton(BadgeService::class);
        $this->container->singleton(LandingPageService::class);
        $this->container->singleton(AcademyAnalyticsService::class);

        $this->loadHelpers(Paths::packagePath('Academy/Helpers/academy.php'));
    }

    public function boot(): void
    {
        $this->registerMacros();
        $this->registerEventListeners();
    }

    /**
     * Register model macros for the User model.
     * This allows $user->enrolments(), $user->isEnrolledIn($program), etc.
     */
    protected function registerMacros(): void
    {
        User::macro('enrolments', function () {
            /** @var User $this */
            return $this->hasMany(AcademyEnrolment::class, 'user_id');
        });

        User::macro('isEnrolledIn', function ($program) {
            /** @var User $this */
            $programId = is_object($program) ? $program->id : $program;

            return AcademyEnrolment::where('user_id', $this->id)
                ->where('program_id', $programId)
                ->whereIn('status', ['active', 'completed'])
                ->exists();
        });
    }

    /**
     * Register the package's event listeners.
     */
    protected function registerEventListeners(): void
    {
        // Enrolment & Completion
        Event::listen(EnrolmentCreatedEvent::class, SendEnrolmentNotificationListener::class);
        Event::listen(PaymentReceivedEvent::class, SendPaymentReceiptListener::class);
        Event::listen(PaymentSuccessfulEvent::class, ProcessPaymentSuccessListener::class);
        Event::listen(ProgramCompletedEvent::class, IssueCertificateListener::class);
        Event::listen(ProgramCompletedEvent::class, AwardBadgeListener::class);
        Event::listen(ProgramCompletedEvent::class, SendProgramCompletedNotificationListener::class);

        // Assessments
        Event::listen(AssessmentGradedEvent::class, SendAssessmentGradedListener::class);

        // Live Sessions
        Event::listen(LiveSessionStartingEvent::class, SendLiveSessionReminderListener::class);

        // Financials
        Event::listen(InstalmentOverdueEvent::class, SendInstalmentReminderListener::class);

        // Activity Logging (Internal)
        $loggable = [
            EnrolmentCreatedEvent::class,
            ProgramCompletedEvent::class,
            LessonCompletedEvent::class,
            ModuleCompletedEvent::class,
            AssessmentSubmittedEvent::class,
            AssessmentGradedEvent::class,
            DiscussionRepliedEvent::class,
            BadgeAwardedEvent::class,
        ];

        foreach ($loggable as $event) {
            Event::listen($event, LogActivityListener::class);
            Event::listen($event, AwardLearningRewardListener::class);
        }
    }
}
