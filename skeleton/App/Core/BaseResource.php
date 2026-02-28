<?php

declare(strict_types=1);

/**
 * An abstract base class designed to transform data into structured array and JSON formats,
 * typically used for API responses or data serialization in a consistent way.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Core;

use Core\Resource;

abstract class BaseResource extends Resource
{
    abstract public function toArray(): array;
}
