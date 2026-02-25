<?php

declare(strict_types=1);

namespace Academy\Listeners;

use Academy\Events\AssessmentGradedEvent;
use Academy\Events\BadgeAwardedEvent;
use Academy\Events\LessonCompletedEvent;
use Academy\Events\ModuleCompletedEvent;
use Academy\Events\ProgramCompletedEvent;
use App\Models\User;
use ReflectionClass;
use Wallet\Wallet;

class AwardLearningRewardListener
{
    public function handle(mixed $event): void
    {
        if (!config('academy.rewards.enabled', false) || !config('academy.integrations.wallet', true) || !class_exists(Wallet::class)) {
            return;
        }

        $user = $this->getUserFromEvent($event);
        $amount = $this->getRewardAmount($event);

        if ($user && $amount > 0) {
            // Find wallet by owner to be safe (User macro might not be active or loaded yet)
            $wallet = Wallet::findByOwner($user->id, User::class);

            if ($wallet) {
                Wallet::transaction($wallet->id)
                    ->credit($amount, (string) config('academy.rewards.currency', 'USD'))
                    ->description('Learning Reward: ' . $this->getEventDescription($event))
                    ->meta([
                        'event' => (new ReflectionClass($event))->getShortName(),
                        'source' => 'academy',
                    ])
                    ->execute();
            }
        }
    }

    protected function getUserFromEvent(mixed $event): ?User
    {
        if (isset($event->submission) && isset($event->submission->enrolment)) {
            return $event->submission->enrolment->user;
        }

        if (isset($event->enrolment)) {
            // Ensure relation is loaded if possible, otherwise find by ID
            if ($event->enrolment->user) {
                return $event->enrolment->user;
            }
            if (isset($event->enrolment->user_id)) {
                return User::find($event->enrolment->user_id);
            }
        }

        if (isset($event->user)) {
            return $event->user;
        }

        return null;
    }

    protected function getRewardAmount(mixed $event): float
    {
        $amounts = config('academy.rewards.amounts', []);

        return match (true) {
            $event instanceof ProgramCompletedEvent => (float) ($amounts['program_completed'] ?? 0),
            $event instanceof BadgeAwardedEvent => (float) ($amounts['badge_awarded'] ?? 0),
            $event instanceof LessonCompletedEvent => (float) ($amounts['lesson_completed'] ?? 0),
            $event instanceof ModuleCompletedEvent => (float) ($amounts['module_completed'] ?? 0),
            $event instanceof AssessmentGradedEvent => ($event->submission->grade->is_passing ?? false) ? (float) ($amounts['quiz_passed'] ?? 0) : 0,
            default => 0,
        };
    }

    /**
     * Get human-readable description for the event.
     */
    protected function getEventDescription(mixed $event): string
    {
        return match (true) {
            $event instanceof ProgramCompletedEvent => 'Completed Program: ' . ($event->program->title ?? 'Program'),
            $event instanceof BadgeAwardedEvent => 'Awarded Badge: ' . ($event->badge->name ?? 'Badge'),
            $event instanceof LessonCompletedEvent => 'Completed Lesson: ' . ($event->lesson->title ?? 'Lesson'),
            $event instanceof ModuleCompletedEvent => 'Completed Module: ' . ($event->module->title ?? 'Module'),
            $event instanceof AssessmentGradedEvent => 'Passed Assessment',
            default => 'Learning Milestone',
        };
    }
}
