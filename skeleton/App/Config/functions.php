<?php

declare(strict_types=1);

if (! function_exists('app')) {
    function app(string $value): mixed
    {
        return config("app.{$value}");
    }
}
