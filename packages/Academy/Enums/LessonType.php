<?php

declare(strict_types=1);

namespace Academy\Enums;

enum LessonType: string
{
    case TEXT = 'text';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case PDF = 'pdf';
    case QUIZ = 'quiz';
    case ASSIGNMENT = 'assignment';
    case LIVE = 'live';
    case DOWNLOAD = 'download';
    case EMBED = 'embed';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::VIDEO => 'Video',
            self::AUDIO => 'Audio',
            self::PDF => 'PDF Document',
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Assignment',
            self::LIVE => 'Live Session',
            self::DOWNLOAD => 'Downloadable',
            self::EMBED => 'Embedded Content',
        };
    }
}
