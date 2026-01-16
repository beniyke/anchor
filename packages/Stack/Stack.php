<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for Stack (Form Builder) operations.
 *
 * @method static FormBuilder           form()                                                  Create a new form builder
 * @method static Submission            submit(Form $form, array $data)                         Submit form data
 * @method static Event                 recordEvent(Form $form, string $type, array $data = []) Record analytics event
 * @method static array                 getSubmissionData(Submission|int $submission)           Get flat submission data
 * @method static StackAnalyticsService analytics()                                             Get analytics service
 *
 * @see StackManagerService
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Stack;

use Stack\Models\Event;
use Stack\Models\Form;
use Stack\Models\Submission;
use Stack\Services\Builders\FormBuilder;
use Stack\Services\StackAnalyticsService;
use Stack\Services\StackManagerService;

class Stack
{
    /**
     * Create a new form builder.
     */
    public static function form(): FormBuilder
    {
        return new FormBuilder(resolve(StackManagerService::class));
    }

    public static function submit(Form $form, array $data, ?int $userId = null, ?string $ip = null, ?string $userAgent = null): Submission
    {
        return resolve(StackManagerService::class)->submit($form, $data, $userId, $ip, $userAgent);
    }

    /**
     * Record an analytics event.
     */
    public static function recordEvent(Form $form, string $type, ?int $userId = null, ?string $sessionId = null, array $data = []): mixed
    {
        return resolve(StackManagerService::class)->recordEvent($form, $type, $userId, $sessionId, $data);
    }

    public static function analytics(): StackAnalyticsService
    {
        return resolve(StackAnalyticsService::class);
    }

    /**
     * Forward static calls to StackManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(StackManagerService::class)->$method(...$arguments);
    }
}
