<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use Core\Event;
use Core\Services\ConfigServiceInterface;
use Helpers\Data\Contracts\DataTransferObject;
use Helpers\Http\Request;
use Security\Auth\Events\LoginEvent;
use Security\Auth\Events\LoginFailedEvent;
use Security\Auth\Events\LogoutEvent;
use Security\Auth\Interfaces\AuthManagerInterface;
use Security\Auth\Interfaces\TokenManagerInterface;

class ApiAuthService implements AuthServiceInterface
{
    private string $guard = 'api';

    private ?string $generatedToken = null;

    public function __construct(
        private readonly TokenManagerInterface $token_manager,
        private readonly Request $request,
        private readonly AuthManagerInterface $auth,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function viaGuard(string $guard): self
    {
        $this->guard = $guard;

        return $this;
    }

    public function isAuthenticated(): bool
    {
        return $this->auth->guard($this->guard)->check();
    }

    public function user(): ?User
    {
        return $this->auth->guard($this->guard)->user();
    }

    public function login(DataTransferObject $request): bool
    {
        if (! $request->isValid()) {
            Event::dispatch(new LoginFailedEvent($request->toArray(), $this->guard));

            return false;
        }

        if (! $this->auth->guard($this->guard)->attempt($request->toArray())) {
            Event::dispatch(new LoginFailedEvent($request->toArray(), $this->guard));

            return false;
        }

        $user = $this->user();

        $tokenName = $this->request->post('device_name') ?? 'API Client';
        $abilities = $this->request->post('abilities') ?? ['*'];

        $this->generatedToken = $this->token_manager->createToken(
            $user,
            $tokenName,
            $abilities
        );

        $this->auth->guard($this->guard)->setUser($user);

        Event::dispatch(new LoginEvent($user, false, $this->guard));

        return true;
    }

    public function logout(): bool
    {
        $user = $this->user();

        if ($user) {
            $token = $this->request->getBearerToken();

            if ($token && str_contains($token, '|')) {
                [$id, $secret] = explode('|', $token, 2);
                if (is_numeric($id)) {
                    $this->token_manager->revokeToken((int) $id);
                }
            }

            $this->auth->guard($this->guard)->logout();
            Event::dispatch(new LogoutEvent($user, $this->guard));
        }

        return true;
    }

    public function logoutAll(): bool
    {
        $guards = $this->config->get('auth.guards', []);

        foreach (array_keys($guards) as $guardName) {
            $this->viaGuard($guardName)->logout();
        }

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

    public function getSessionKey(): ?string
    {
        return $this->auth->guard($this->guard)->getSessionKey();
    }

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
}
