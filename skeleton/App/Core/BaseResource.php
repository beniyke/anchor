<?php

declare(strict_types=1);

/**
 * An abstract base class designed to transform data into structured array and JSON formats,
 * typically used for API responses or data serialization in a consistent way.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

namespace App\Core;

use JsonSerializable;

abstract class BaseResource implements JsonSerializable
{
    protected $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    abstract public function toArray(): array;

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public static function collection(array $resources): array
    {
        return array_map(function ($resource) {
            return (new static($resource))->toArray();
        }, $resources);
    }

    /**
     * This ensures the object can be converted to JSON
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
