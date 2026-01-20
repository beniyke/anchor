<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * TurnstileDriver class.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Drivers;

use Helpers\Http\Client\Curl;

class TurnstileDriver implements CaptchaDriverInterface
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    private const JS_URL = 'https://challenges.cloudflare.com/turnstile/v0/api.js';

    public function __construct(
        private readonly string $secret,
        private readonly string $siteKey
    ) {
    }

    public function verify(string $token, ?string $ip = null): bool
    {
        if (empty($token)) {
            return false;
        }

        $response = (new Curl())->post(self::VERIFY_URL, [
            'secret' => $this->secret,
            'response' => $token,
            'remoteip' => $ip,
        ])->send();

        $data = $response->json();

        return $data['success'] ?? false;
    }

    public function render(array $attributes = []): string
    {
        $attrStr = '';
        foreach ($attributes as $key => $value) {
            $attrStr .= sprintf(' %s="%s"', $key, $value);
        }

        return sprintf(
            '<div class="cf-turnstile" data-sitekey="%s"%s></div>',
            $this->siteKey,
            $attrStr
        );
    }

    public function getScript(): string
    {
        return sprintf('<script src="%s" async defer></script>', self::JS_URL);
    }
}
