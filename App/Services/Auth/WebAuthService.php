<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\MenuService;
use Core\Event;
use Core\Services\ConfigServiceInterface;
use Helpers\Data\Contracts\DataTransferObject;
use Helpers\Http\Flash;
use Security\Auth\Events\LoginEvent;
use Security\Auth\Events\LoginFailedEvent;
use Security\Auth\Events\LogoutEvent;
use Security\Auth\Interfaces\AuthManagerInterface;

class WebAuthService implements AuthServiceInterface
{
    private string $guard = 'web';

    public function __construct(
        private readonly Flash $flash,
        private readonly MenuService $menu_service,
        private readonly AuthManagerInterface $auth,
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function viaGuard(string $guard): self
    {
        $this->guard = $guard;

        return $this;
    }

    public function user(): ?User
    {
        return $this->auth->guard($this->guard)->user();
    }

    public function login(DataTransferObject $request): bool
    {
        if (! $request->isValid()) {
            $this->flash->error('Invalid login credentials.');
            Event::dispatch(new LoginFailedEvent($request->toArray(), $this->guard));

            return false;
        }

        if (! $this->auth->guard($this->guard)->attempt($request->toArray())) {
            $this->flash->error('Invalid login credentials.');
            Event::dispatch(new LoginFailedEvent($request->toArray(), $this->guard));

            return false;
        }

        $user = $this->user();

        $remember = method_exists($request, 'hasRememberMe') && $request->hasRememberMe();
        Event::dispatch(new LoginEvent($user, $remember, $this->guard));

        return true;
    }

    public function logout(): bool
    {
        $user = $this->user();

        if ($user) {
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

    public function isAuthenticated(): bool
    {
        return $this->auth->guard($this->guard)->check();
    }

    public function isAuthorized(string $route): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        return $user->canLogin() && $this->canAccessRoute($user, $route);
    }

    private function canAccessRoute(User $user, string $route): bool
    {
        return in_array($route, $this->menu_service->getAccessibleRoutes($user));
    }

    public function getSessionKey(): ?string
    {
        return $this->auth->guard($this->guard)->getSessionKey();
    }
}
