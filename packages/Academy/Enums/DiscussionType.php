<?php

declare(strict_types=1);

namespace Academy\Enums;

enum DiscussionType: string
{
    case GENERAL = 'general';
    case LESSON = 'lesson';
    case PROGRAM = 'program';
    case ANNOUNCEMENT = 'announcement';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL => 'General Discussion',
            self::LESSON => 'Lesson Question',
            self::PROGRAM => 'Program Feed',
            self::ANNOUNCEMENT => 'Announcement',
        };
    }
}
