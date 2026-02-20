<?php

declare(strict_types=1);

namespace Rank;

use Rank\Services\SeoManager;

class Rank
{
    public static function setTitle(string $title): SeoManager
    {
        return resolve(SeoManager::class)->setTitle($title);
    }

    public static function setDescription(string $description): SeoManager
    {
        return resolve(SeoManager::class)->setDescription($description);
    }

    public static function setImage(string $url): SeoManager
    {
        return resolve(SeoManager::class)->setImage($url);
    }

    public static function setCanonical(string $url): SeoManager
    {
        return resolve(SeoManager::class)->setCanonical($url);
    }

    public static function setMeta(string $name, string $content): SeoManager
    {
        return resolve(SeoManager::class)->setMeta($name, $content);
    }

    public static function setOg(string $property, string $content): SeoManager
    {
        return resolve(SeoManager::class)->setOg($property, $content);
    }

    public static function render(): string
    {
        return resolve(SeoManager::class)->render();
    }
}
