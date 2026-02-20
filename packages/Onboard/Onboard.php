<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboard.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard;

use App\Models\User;
use BadMethodCallException;
use DateTimeInterface;
use Onboard\Models\DocumentUpload;
use Onboard\Models\Onboarding;
use Onboard\Models\Task;
use Onboard\Models\TaskCompletion;
use Onboard\Models\Template;
use Onboard\Services\Builders\OnboardingBuilder;
use Onboard\Services\Builders\TaskBuilder;
use Onboard\Services\EquipmentManagerService;
use Onboard\Services\OnboardAnalyticsService;
use Onboard\Services\OnboardManagerService;
use Onboard\Services\TrainingManagerService;

/**
 * Onboard Facade
 *
 * @method static Onboarding     startOnboarding(User $user, Template $template, ?DateTimeInterface $dueAt = null)
 * @method static TaskCompletion completeTask(User $user, Task $task, ?string $notes = null)
 * @method static void           verifyDocument(DocumentUpload $upload, User $verifier)
 * @method static Equipment      requestEquipment(User $user, string $type, ?string $notes = null)
 */
class Onboard
{
    /**
     * Get the OnboardManagerService instance.
     */
    protected static function manager(): OnboardManagerService
    {
        return resolve(OnboardManagerService::class);
    }

    /**
     * Start building a new onboarding process.
     */
    public static function onboarding(): OnboardingBuilder
    {
        return new OnboardingBuilder(static::manager());
    }

    /**
     * Start building a new task.
     */
    public static function task(): TaskBuilder
    {
        return new TaskBuilder();
    }

    public static function analytics(): OnboardAnalyticsService
    {
        return resolve(OnboardAnalyticsService::class);
    }

    public static function training(): TrainingManagerService
    {
        return resolve(TrainingManagerService::class);
    }

    public static function equipment(): EquipmentManagerService
    {
        return resolve(EquipmentManagerService::class);
    }

    /**
     * Delegate static calls to the manager.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $manager = static::manager();
        if (method_exists($manager, $method)) {
            return $manager->$method(...$arguments);
        }

        $equipment = static::equipment();
        if (method_exists($equipment, $method)) {
            return $equipment->$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on Onboard facade.");
    }
}
