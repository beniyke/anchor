<?php

declare(strict_types=1);

namespace Shield\Drivers;

use Helpers\Http\Client\Curl;

class GoogleRecaptchaDriver implements CaptchaDriverInterface
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    private const JS_URL = 'https://www.google.com/recaptcha/api.js';

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
            '<div class="g-recaptcha" data-sitekey="%s"%s></div>',
            $this->siteKey,
            $attrStr
        );
    }

    public function getScript(): string
    {
        return sprintf('<script src="%s" async defer></script>', self::JS_URL);
    }
}
