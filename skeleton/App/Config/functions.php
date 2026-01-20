<?php

declare(strict_types=1);

use App\Services\ActivityLoggerService;

if (! function_exists('app')) {
    function app(string $value): mixed
    {
        return config("app.{$value}");
    }
}

if (! function_exists('activity')) {
    function activity(string $description, ?array $data = null, ?int $user_id = null): bool
    {
        $activity = resolve(ActivityLoggerService::class);

        return $activity->description($description)
            ->data($data)
            ->user($user_id)
            ->log();
    }
}
