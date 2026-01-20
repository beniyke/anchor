<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ShieldManagerService class.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Shield\Services;

use Audit\Audit;
use Core\Services\ConfigServiceInterface;
use InvalidArgumentException;
use Shield\Drivers\CaptchaDriverInterface;
use Shield\Drivers\GoogleRecaptchaDriver;
use Shield\Drivers\TurnstileDriver;
use Throwable;

class ShieldManagerService
{
    private array $drivers = [];

    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('shield.default', 'recaptcha');
    }

    public function driver(?string $name = null): CaptchaDriverInterface
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->drivers[$name])) {
            return $this->drivers[$name];
        }

        return $this->drivers[$name] = $this->resolve($name);
    }

    protected function resolve(string $name): CaptchaDriverInterface
    {
        $config = $this->config->get("shield.drivers.{$name}");

        if (! $config) {
            throw new InvalidArgumentException("Captcha driver [{$name}] is not defined.");
        }

        switch ($name) {
            case 'recaptcha':
                return new GoogleRecaptchaDriver(
                    secret: $config['secret'] ?? '',
                    siteKey: $config['site_key'] ?? ''
                );
            case 'turnstile':
                return new TurnstileDriver(
                    secret: $config['secret'] ?? '',
                    siteKey: $config['site_key'] ?? ''
                );
            default:
                throw new InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }

    /**
     * Proxy method to the driver's verify method with auditing.
     */
    public function verify(string $token, ?string $ip = null): bool
    {
        $driverName = $this->getDefaultDriver();
        $driver = $this->driver($driverName);

        try {
            $isValid = $driver->verify($token, $ip);

            // Log analytics via Audit
            if (class_exists(Audit::class)) {
                Audit::make()
                    ->event($isValid ? 'captcha.verified' : 'captcha.failed')
                    ->metadata([
                        'driver' => $driverName,
                        'ip' => $ip,
                    ])
                    ->log();
            }

            return $isValid;
        } catch (Throwable $e) {
            // Log error
            if (class_exists(Audit::class)) {
                Audit::make()
                    ->event('captcha.error')
                    ->metadata([
                        'driver' => $driverName,
                        'error' => $e->getMessage(),
                        'ip' => $ip,
                    ])
                    ->log();
            }

            return false;
        }
    }

    /**
     * Get the HTML script/widget for the frontend.
     */
    public function render(?string $driver = null, array $attributes = []): string
    {
        return $this->driver($driver)->render($attributes);
    }

    /**
     * Get the script tag URL/content.
     */
    public function getScript(?string $driver = null): string
    {
        return $this->driver($driver)->getScript();
    }

    public function analytics(): ShieldAnalytics
    {
        return new ShieldAnalytics();
    }
}
