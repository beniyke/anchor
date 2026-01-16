<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Otp Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Services;

use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Verify\Models\OtpCode;

class OtpBuilderService
{
    private string $identifier;

    private string $code;

    private string $channel = 'email';

    private ?string $expiresAt = null;

    private ?string $verifiedAt = null;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function withCode(string $code): self
    {
        $this->code = password_hash($code, PASSWORD_DEFAULT);

        return $this;
    }

    public function via(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function expiresAt(string $dateTime): self
    {
        $this->expiresAt = $dateTime;

        return $this;
    }

    public function expired(int $minutes = 1): self
    {
        $this->expiresAt = DateTimeHelper::now()->subMinutes($minutes)->toDateTimeString();

        return $this;
    }

    public function verified(): self
    {
        $this->verifiedAt = DateTimeHelper::now()->toDateTimeString();

        return $this;
    }

    public function save(): OtpCode
    {
        return OtpCode::create([
            'identifier' => $this->identifier,
            'refid' => Str::random('secure'),
            'code' => $this->code,
            'channel' => $this->channel,
            'expires_at' => $this->expiresAt ?? DateTimeHelper::now()->addMinutes(15)->toDateTimeString(),
            'verified_at' => $this->verifiedAt,
        ]);
    }
}
