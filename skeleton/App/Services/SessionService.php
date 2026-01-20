<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Session;
use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Helpers\DateTimeHelper;
use Helpers\Http\UserAgent;
use Helpers\Lottery;
use Helpers\String\Str;
use RuntimeException;

class SessionService
{
    protected readonly UserAgent $agent;

    protected readonly ConfigServiceInterface $config;

    public function __construct(UserAgent $agent, ConfigServiceInterface $config)
    {
        $this->agent = $agent;
        $this->config = $config;
    }

    public function createNewSession(User $user, ?int $lifetime = null): Session
    {
        $token = Str::random('secure', 32);

        if (empty($lifetime)) {
            $lifetime = (int) $this->config->get('session.timeout');
        }

        $expiresAt = DateTimeHelper::now()->addSeconds($lifetime);

        $session = Session::create([
            'user_id' => $user->id,
            'token' => $token,
            'browser' => $this->agent->browser() . ' ' . $this->agent->version(),
            'device' => $this->agent->device(),
            'ip' => $this->agent->ip(),
            'os' => $this->agent->platform(),
            'expires_at' => $expiresAt,
        ]);

        // Automatic session garbage collection
        $this->handleGarbageCollection();

        return $session;
    }

    /**
     * Garbage collection for expired sessions.
     * Runs based on configured lottery odds to balance performance.
     */
    private function handleGarbageCollection(): void
    {
        $lottery = $this->config->get('session.lottery');
        $chances = $lottery[0] ?? 2;
        $outOf = $lottery[1] ?? 100;

        Lottery::odds($chances, $outOf)
            ->winner(fn () => $this->pruneExpiredSessions())
            ->choose();
    }

    public function getSessionByToken(string $token): ?Session
    {
        return Session::findByToken($token);
    }

    public function isSessionValid(?Session $session): bool
    {
        if (! $session) {
            return false;
        }

        $timeoutSeconds = (int) $this->config->get('session.timeout');

        return $session->isValid($timeoutSeconds);
    }

    public function refreshSession(Session $session): bool
    {
        return $session->refresh();
    }

    public function reissueToken(Session $session): string
    {
        $session->token = Str::random('secure');

        if ($session->save()) {
            return $session->token;
        }

        throw new RuntimeException("Failed to save the new token for session ID: {$session->id}");
    }

    public function terminateSession(string $token): bool
    {
        return Session::deleteByToken($token) > 0;
    }

    public function terminateAllUserSessions(int $userId): int
    {
        return Session::query()
            ->forUser($userId)
            ->delete();
    }

    public function terminateOtherSessions(Session $currentSession): int
    {
        return Session::query()
            ->forUser($currentSession->user_id)
            ->whereNotEqual('id', $currentSession->id)
            ->delete();
    }

    public function getAllUserSessions(int $userId): ?array
    {
        return Session::query()->forUser($userId)->get();
    }

    public function pruneExpiredSessions(): int
    {
        return Session::pruneExpired();
    }
}
