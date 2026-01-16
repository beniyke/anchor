<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Verify Manager Service
 *
 * Main orchestration service for OTP verification workflow
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Core\Services\ConfigServiceInterface;
use Exception;
use Verify\Contracts\ChannelInterface;
use Verify\Contracts\OtpGeneratorInterface;
use Verify\Contracts\OtpStorageInterface;
use Verify\Exceptions\ChannelNotFoundException;
use Verify\Exceptions\OtpNotFoundException;

class VerifyManagerService
{
    public function __construct(
        private readonly OtpGeneratorInterface $generator,
        private readonly OtpStorageInterface $storage,
        private readonly RateLimiterService $rateLimiter,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function generate(string $identifier, string $channel): string
    {
        $identifier = $this->normalizeIdentifier($identifier);
        $rateLimit = $this->config->get('verify.rate_limit_generation', 3);
        $window = $this->config->get('verify.rate_limit_window_minutes', 60);

        $this->rateLimiter->checkGeneration($identifier, $rateLimit, $window);

        $codeLength = $this->config->get('verify.code_length', 6);
        $code = $this->generator->generate($codeLength);
        $expiresIn = $this->config->get('verify.expiration_minutes', 15);

        $this->storage->store($identifier, $code, $channel, $expiresIn);
        $this->rateLimiter->incrementGeneration($identifier);

        return $code;
    }

    public function send(string $identifier, string $channel, ?string $receiverName = null): bool
    {
        $code = $this->generate($identifier, $channel);
        $expiresIn = $this->config->get('verify.expiration_minutes', 15);

        try {
            $channelInstance = $this->resolveChannel($channel);
            $sent = $channelInstance->send($identifier, $code, $receiverName);

            if (! $sent) {
                logger('verify.log')->warning('OTP send failed via channel', [
                    'identifier' => $this->maskIdentifier($identifier),
                    'channel' => $channel,
                ]);

                return false;
            }

            logger('verify.log')->info('OTP generated and sent', [
                'identifier' => $this->maskIdentifier($identifier),
                'channel' => $channel,
                'expires_in_minutes' => $expiresIn,
            ]);

            return true;
        } catch (Exception $e) {
            logger('verify.log')->error('OTP channel send error', [
                'identifier' => $this->maskIdentifier($identifier),
                'channel' => $channel,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    public function verify(string $identifier, string $code): bool
    {
        $identifier = $this->normalizeIdentifier($identifier);
        $maxAttempts = $this->config->get('verify.max_verification_attempts', 5);
        $window = $this->config->get('verify.rate_limit_window_minutes', 60);

        $this->rateLimiter->checkVerification($identifier, $maxAttempts, $window);
        $this->rateLimiter->incrementVerification($identifier);

        try {
            $isValid = $this->storage->verify($identifier, $code);

            if ($isValid) {
                $this->storage->markAsVerified($identifier);
                $this->rateLimiter->reset($identifier, 'verification');
                $this->rateLimiter->reset($identifier, 'generation');

                logger('verify.log')->info('OTP verified successfully', [
                    'identifier' => $this->maskIdentifier($identifier),
                ]);
            }

            return $isValid;
        } catch (Exception $e) {
            logger('verify.log')->warning('OTP verification failed', [
                'identifier' => $this->maskIdentifier($identifier),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function resend(string $identifier, string $channel, ?string $receiverName = null): bool
    {
        $identifier = $this->normalizeIdentifier($identifier);
        $rateLimit = $this->config->get('verify.rate_limit_generation', 3);
        $window = $this->config->get('verify.rate_limit_window_minutes', 60);

        $this->rateLimiter->checkGeneration($identifier, $rateLimit, $window);

        $otp = $this->storage->get($identifier);

        if (! $otp) {
            throw new OtpNotFoundException($identifier);
        }

        logger('verify.log')->info('OTP resend requested', [
            'identifier' => $this->maskIdentifier($identifier),
            'channel' => $channel,
        ]);

        return $this->send($identifier, $channel, $receiverName);
    }

    public function delete(string $identifier): bool
    {
        $identifier = $this->normalizeIdentifier($identifier);

        logger('verify.log')->info('OTP deleted', [
            'identifier' => $this->maskIdentifier($identifier),
        ]);

        return $this->storage->delete($identifier);
    }

    public function hasPending(string $identifier): bool
    {
        $identifier = $this->normalizeIdentifier($identifier);

        return $this->storage->hasPending($identifier);
    }

    public function otp(string $identifier): OtpBuilderService
    {
        return new OtpBuilderService($this->normalizeIdentifier($identifier));
    }

    public function analytics(): VerifyAnalyticsService
    {
        return resolve(VerifyAnalyticsService::class);
    }

    private function resolveChannel(string $channelName): ChannelInterface
    {
        $channels = $this->config->get('verify.channels', []);

        if (! isset($channels[$channelName])) {
            throw new ChannelNotFoundException($channelName);
        }

        $channelClass = $channels[$channelName];

        return resolve($channelClass);
    }

    private function normalizeIdentifier(string $identifier): string
    {
        return strtolower(trim($identifier));
    }

    private function maskIdentifier(string $identifier): string
    {
        if (str_contains($identifier, '@')) {
            [$local, $domain] = explode('@', $identifier, 2);
            $maskedLocal = substr($local, 0, 2) . str_repeat('*', max(0, strlen($local) - 2));

            return $maskedLocal . '@' . $domain;
        }

        if (strlen($identifier) > 5) {
            return substr($identifier, 0, 3) . str_repeat('*', strlen($identifier) - 5) . substr($identifier, -2);
        }

        return str_repeat('*', strlen($identifier));
    }
}
