<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * HasShield trait.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Traits;

use Shield\Helpers\ShieldHelper;

trait HasShield
{
    public function captcha(?string $driver = null): ShieldHelper
    {
        return shield($driver);
    }

    /**
     * Conveniently get the script tag.
     */
    public function captchaScript(?string $driver = null): string
    {
        return shield($driver)->script();
    }

    /**
     * Conveniently get the widget.
     */
    public function captchaWidget(?string $driver = null, array $attributes = []): string
    {
        return shield($driver)->render($attributes);
    }
}
