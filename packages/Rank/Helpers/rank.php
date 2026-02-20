<?php

declare(strict_types=1);

use Rank\Rank;
use Rank\Services\SeoManager;

if (! function_exists('rank')) {
    function rank(): Rank|SeoManager
    {
        return resolve(Rank::class);
    }
}
