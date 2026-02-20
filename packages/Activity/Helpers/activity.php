<?php

declare(strict_types=1);

use Activity\Activity;

if (! function_exists('activity')) {
    function activity(string $description, ?array $data = null, ?int $user_id = null): bool
    {
        return Activity::description($description)
            ->data($data)
            ->user($user_id)
            ->log();
    }
}
