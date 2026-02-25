<?php

declare(strict_types=1);

namespace Academy\Enums;

enum VideoProvider: string
{
    case YOUTUBE = 'youtube';
    case VIMEO = 'vimeo';
    case WISTIA = 'wistia';
    case BUNNY = 'bunny';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::YOUTUBE => 'YouTube',
            self::VIMEO => 'Vimeo',
            self::WISTIA => 'Wistia',
            self::BUNNY => 'Bunny Stream',
            self::CUSTOM => 'Custom URL',
        };
    }
}
