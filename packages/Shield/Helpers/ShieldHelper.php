<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ShieldHelper class.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Helpers;

use Shield\Services\ShieldManagerService;

class ShieldHelper
{
    public function __construct(
        private readonly ShieldManagerService $manager,
        private readonly ?string $driver = null
    ) {
    }

    public function driver(string $driver): self
    {
        return new self($this->manager, $driver);
    }

    public function render(array $attributes = []): string
    {
        return $this->manager->render($this->driver, $attributes);
    }

    public function script(): string
    {
        return $this->manager->getScript($this->driver);
    }

    /**
     * Render both script and widget (helper).
     */
    public function all(array $attributes = []): string
    {
        return $this->script() . PHP_EOL . $this->render($attributes);
    }
}
