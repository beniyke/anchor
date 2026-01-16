<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Link scopes enumeration.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Enums;

enum LinkScope: string
{
    case VIEW = 'view';
    case DOWNLOAD = 'download';
    case EDIT = 'edit';
    case JOIN = 'join';
    case SHARE = 'share';

    public function label(): string
    {
        return match ($this) {
            self::VIEW => 'View',
            self::DOWNLOAD => 'Download',
            self::EDIT => 'Edit',
            self::JOIN => 'Join',
            self::SHARE => 'Share',
        };
    }
}
