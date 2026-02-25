<?php

declare(strict_types=1);

namespace Academy\Enums;

enum ResourceType: string
{
    case FILE = 'file';
    case VIDEO_EXTERNAL = 'video_external';
    case LINK = 'link';
    case EMBED = 'embed';

    public function label(): string
    {
        return match ($this) {
            self::FILE => 'Uploaded File',
            self::VIDEO_EXTERNAL => 'External Video',
            self::LINK => 'External Link',
            self::EMBED => 'Embed Code',
        };
    }
}
