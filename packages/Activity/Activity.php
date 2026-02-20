<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Activity logger facade.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity;

use Activity\Services\ActivityAnalytics;
use Activity\Services\ActivityManagerService;
use Database\BaseModel;

class Activity
{
    public static function description(string $description): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->description($description);
    }

    public static function data(?array $data = null): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->data($data);
    }

    public static function user(?int $user_id = null): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->user($user_id);
    }

    public static function subject(?BaseModel $subject): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->subject($subject);
    }

    public static function metadata(array $metadata): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->metadata($metadata);
    }

    public static function tag(string $tag): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->tag($tag);
    }

    public static function level(string $level): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->level($level);
    }

    public static function analytics(): ActivityAnalytics
    {
        return resolve(ActivityAnalytics::class);
    }

    public static function immediate(bool $immediate = true): ActivityManagerService
    {
        return resolve(ActivityManagerService::class)->immediate($immediate);
    }

    public static function log(): bool
    {
        return resolve(ActivityManagerService::class)->log();
    }
}
