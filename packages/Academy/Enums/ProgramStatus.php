<?php

declare(strict_types=1);

namespace Academy\Enums;

enum ProgramStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::ARCHIVED => 'Archived',
            self::SUSPENDED => 'Suspended',
        };
    }
}
