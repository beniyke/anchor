<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core link manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Services;

use Audit;
use Core\Services\ConfigServiceInterface;
use Database\BaseModel;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Link\Enums\LinkScope;
use Link\Exceptions\InvalidLinkException;
use Link\Exceptions\LinkExpiredException;
use Link\Exceptions\LinkRevokedException;
use Link\Exceptions\LinkUsageExceededException;
use Link\Models\Link;
use Link\Models\LinkUsage;

class LinkManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function create(array $data): Link
    {
        $token = $this->generateToken();
        $tokenHash = $this->hashToken($token);

        $expiresAt = $data['expires_at'] ?? null;
        if ($expiresAt === null && isset($data['valid_for_hours'])) {
            $expiresAt = DateTimeHelper::now()->addHours($data['valid_for_hours']);
        } elseif ($expiresAt === null) {
            $defaultHours = $this->config->get('link.default_expiry_hours', 24);
            $expiresAt = DateTimeHelper::now()->addHours($defaultHours);
        }

        $scopes = $data['scopes'] ?? [LinkScope::VIEW->value];
        if (is_array($scopes)) {
            $scopes = array_map(fn ($s) => $s instanceof LinkScope ? $s->value : $s, $scopes);
        }

        $link = Link::create([
            'refid' => Str::random('secure'),
            'token' => $tokenHash,
            'linkable_type' => $data['linkable_type'] ?? null,
            'linkable_id' => $data['linkable_id'] ?? null,
            'scopes' => $scopes,
            'recipient_type' => $data['recipient_type'] ?? null,
            'recipient_value' => $data['recipient_value'] ?? null,
            'max_uses' => $data['max_uses'] ?? null,
            'use_count' => 0,
            'expires_at' => $expiresAt,
            'metadata' => $data['metadata'] ?? [],
            'created_by' => $data['created_by'] ?? null,
        ]);

        // Store unhashed token in the model temporarily for URL generation
        $link->plain_token = $token;

        // Auto-log to Audit if available
        $this->logToAudit('link.created', $link, $data['created_by'] ?? null);

        return $link;
    }

    /**
     * Validate a token and return the link.
     *
     * @throws InvalidLinkException
     * @throws LinkExpiredException
     * @throws LinkRevokedException
     * @throws LinkUsageExceededException
     */
    public function validate(string $token): Link
    {
        $tokenHash = $this->hashToken($token);
        $link = Link::findByToken($tokenHash);

        if ($link === null) {
            throw new InvalidLinkException();
        }

        if ($link->isRevoked()) {
            throw new LinkRevokedException();
        }

        if ($link->isExpired()) {
            throw new LinkExpiredException();
        }

        if ($link->isExhausted()) {
            throw new LinkUsageExceededException();
        }

        return $link;
    }

    /**
     * Validate without throwing exceptions.
     */
    public function validateSafe(string $token): ?Link
    {
        try {
            return $this->validate($token);
        } catch (InvalidLinkException | LinkExpiredException | LinkRevokedException | LinkUsageExceededException) {
            return null;
        }
    }

    public function isValid(string $token): bool
    {
        return $this->validateSafe($token) !== null;
    }

    /**
     * Revoke a link by token.
     */
    public function revoke(string $token): void
    {
        $tokenHash = $this->hashToken($token);
        $link = Link::findByToken($tokenHash);

        if ($link !== null) {
            $link->revoke();
        }
    }

    /**
     * Revoke a link by refid.
     */
    public function revokeByRefid(string $refid): void
    {
        $link = Link::findByRefid($refid);

        if ($link !== null) {
            $link->revoke();
        }
    }

    /**
     * Record usage of a link.
     */
    public function recordUsage(Link $link, array $metadata = []): LinkUsage
    {
        $link->incrementUseCount();

        return LinkUsage::create([
            'link_id' => $link->id,
            'used_at' => DateTimeHelper::now(),
            'ip_address' => $metadata['ip_address'] ?? null,
            'user_agent' => $metadata['user_agent'] ?? null,
            'metadata' => $metadata['extra'] ?? [],
        ]);
    }

    public function generateSignedUrl(Link $link, string $baseUrl = ''): string
    {
        $token = $link->plain_token ?? '';

        if (empty($token)) {
            // Cannot generate URL without the plain token
            return $baseUrl . '/link/' . $link->refid;
        }

        $signature = $this->sign($token, $link->expires_at?->timestamp ?? 0);

        return $baseUrl . '/link/' . $link->refid . '?token=' . $token . '&sig=' . $signature;
    }

    /**
     * Verify a signed URL signature.
     */
    public function verifySignature(string $token, int $expiresTimestamp, string $signature): bool
    {
        $expected = $this->sign($token, $expiresTimestamp);

        return hash_equals($expected, $signature);
    }

    /**
     * Generate a cryptographically secure token.
     */
    public function generateToken(): string
    {
        $length = $this->config->get('link.token_length', 64);

        return bin2hex(random_bytes((int) ceil($length / 2)));
    }

    /**
     * Hash a token for storage.
     */
    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Sign data for URL verification.
     */
    private function sign(string $token, int $expires): string
    {
        $key = $this->config->get('link.signing_key') ?? $this->config->get('app.key', 'anchor-secret');

        return hash_hmac('sha256', $token . '|' . $expires, $key);
    }

    /**
     * Clean up expired links.
     */
    public function cleanup(): int
    {
        $retentionDays = $this->config->get('link.retention_days', 30);
        $cutoff = DateTimeHelper::now()->subDays($retentionDays);

        $expired = Link::expired()
            ->where('expires_at', '<', $cutoff)
            ->get();

        $count = 0;
        foreach ($expired as $link) {
            $link->delete();
            $count++;
        }

        return $count;
    }

    public function getLinksForResource(BaseModel $resource): array
    {
        return Link::forResource(get_class($resource), $resource->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function getActiveLinksForResource(BaseModel $resource): array
    {
        return Link::forResource(get_class($resource), $resource->id)
            ->valid()
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    /**
     * Log to Audit if the Audit package is available.
     */
    private function logToAudit(string $event, Link $link, ?int $userId = null): void
    {
        $auditClass = 'Audit\Audit';

        if (!class_exists($auditClass)) {
            return;
        }

        $builder = $auditClass::make()
            ->event($event)
            ->on($link)
            ->with('link_refid', $link->refid)
            ->with('linkable_type', $link->linkable_type)
            ->with('linkable_id', $link->linkable_id)
            ->with('scopes', $link->scopes)
            ->with('expires_at', $link->expires_at);

        if ($userId !== null) {
            $builder->by($userId);
        }

        $builder->log();
    }
}
