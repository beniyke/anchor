<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * CaptchaDriverInterface interface.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Drivers;

interface CaptchaDriverInterface
{
    public function verify(string $token, ?string $ip = null): bool;

    /**
     * Get the HTML script/widget for the frontend.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function render(array $attributes = []): string;

    /**
     * Get the script tag URL/content.
     *
     * @return string
     */
    public function getScript(): string;
}
