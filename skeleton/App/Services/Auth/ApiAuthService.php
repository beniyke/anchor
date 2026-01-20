<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\InApp\LoginInAppNotification;
use App\Requests\LoginRequest;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\UserService;
use Bridge\ApiAuth\Contracts\ApiTokenValidatorServiceInterface;
use Bridge\TokenManager;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\Http\Request;
use Helpers\Http\UserAgent;
use Security\Firewall\Drivers\AuthFirewall;

class ApiAuthService implements AuthServiceInterface
{
    private ?User $user = null;

    private ?string $generatedToken = null;

    public function __construct(
        private readonly ApiTokenValidatorServiceInterface $token_validator,
        private readonly UserService $user_service,
        private readonly TokenManager $token_manager,
        private readonly AuthFirewall $firewall,
        private readonly UserAgent $agent,
        private readonly Request $request
    ) {
        $this->user = $token_validator->getAuthenticatedUser();
    }

    public function isAuthenticated(): bool
    {
        return ! empty($this->user);
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function login(LoginRequest $request): bool
    {
        if (! $request->isValid()) {
            $this->firewall->fail()->capture();

            return false;
        }

        $user = $this->user_service->confirmUser($request->getData());

        if (! $user) {
            $this->firewall->fail()->capture();

            return false;
        }

        $this->user = $user;

        // Create Bridge Token
        $tokenName = $this->request->post('device_name') ?? 'API Client';

        // Allow custom abilities from request, default to all
        $abilities = $this->request->post('abilities') ?? ['*'];

        $this->generatedToken = $this->token_manager->createToken(
            $user,
            $tokenName,
            $abilities
        );

        $this->firewall->clear()->capture();
        $this->notifyLoginActivity($user);

        return true;
    }

    public function logout(?string $token = null): bool
    {
        if (! $token) {
            $token = $this->request->getBearerToken();
        }

        if (! $token) {
            return true; // No token to revoke is success
        }

        // Handle personal access tokens (format: id|secret)
        if (str_contains($token, '|')) {
            [$id, $secret] = explode('|', $token, 2);
            if (is_numeric($id)) {
                $revoked = $this->token_manager->revokeToken((int) $id);

                // Clear user state after successful logout
                if ($revoked) {
                    $this->user = null;
                }

                return $revoked;
            }
        }

        // They are handled differently by their respective validators
        return true;
    }

    public function getGeneratedToken(): ?string
    {
        return $this->generatedToken;
    }

    public function isAuthorized(string $route): bool
    {
        return $this->isAuthenticated();
    }

    /**
     * Check if authenticated user's token has specific ability/abilities.
     *
     * @param array|string $abilities Single ability or array of abilities
     *
     * @return bool True if token has all specified abilities
     */
    public function can(string|array $abilities): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        $token = $this->request->getBearerToken();

        if (! $token) {
            return false;
        }

        $abilities = is_array($abilities) ? $abilities : [$abilities];

        foreach ($abilities as $ability) {
            if (! $this->token_manager->checkAbility($token, $ability)) {
                return false;
            }
        }

        return true;
    }

    private function notifyLoginActivity(User $user): void
    {
        $payload = Data::make($user->only(['id', 'name', 'email']));

        defer(function () use ($payload, $user) {
            $data['browser'] = $this->agent->browser();
            $data['period'] = DateTimeHelper::now()->format('D, M d Y h:i A');

            activity('logged in via API', $data, $user->id);

            notify('in-app')
                ->with(LoginInAppNotification::class, $payload->add($data))
                ->send();
        });
    }
}
