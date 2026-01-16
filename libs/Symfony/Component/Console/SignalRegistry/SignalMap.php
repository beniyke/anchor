<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\SignalRegistry;

use const ARRAY_FILTER_USE_KEY;

use function extension_loaded;

use ReflectionExtension;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SignalMap
{
    private static array $map;

    public static function getSignalName(int $signal): ?string
    {
        if (! extension_loaded('pcntl')) {
            return null;
        }

        if (! isset(self::$map)) {
            $r = new ReflectionExtension('pcntl');
            $c = $r->getConstants();
            $map = array_filter($c, fn ($k) => str_starts_with($k, 'SIG') && ! str_starts_with($k, 'SIG_') && $k !== 'SIGBABY', ARRAY_FILTER_USE_KEY);
            self::$map = array_flip($map);
        }

        return self::$map[$signal] ?? null;
    }
}
