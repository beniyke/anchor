<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\InApp\LoginInAppNotification;
use App\Requests\LoginRequest;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\MenuService;
use App\Services\SessionService;
use App\Services\UserService;
use Core\Services\ConfigServiceInterface;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\Http\Flash;
use Helpers\Http\Session;
use Helpers\Http\UserAgent;
use Notify\Notify;
use Security\Firewall\Drivers\AuthFirewall;

class WebAuthService implements AuthServiceInterface
{
    private ?User $user = null;

    private ?string $session_token = null;

    public function __construct(private readonly SessionService $session_service, private readonly UserService $user_service, private readonly Flash $flash, private readonly AuthFirewall $firewall, private readonly MenuService $menu_service, private readonly ConfigServiceInterface $config, private readonly Session $session, private readonly UserAgent $agent)
    {
    }

    public function user(): ?User
    {
        if (empty($this->user)) {
            $this->session_token = $this->session->get($this->config->get('session.name'));

            if (empty($this->session_token)) {
                return null;
            }

            $session = $this->session_service->getSessionByToken($this->session_token);

            if ($session && $this->session_service->isSessionValid($session)) {
                $this->session_service->refreshSession($session);
                $this->user = $session->user;
            }
        }

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
            $this->flash->error('Invalid login credentials.');
            $this->firewall->fail()->capture();

            return false;
        }

        $should_remember = $request->hasRememberMe();
        $long_lifetime = $this->config->get('session.cookie.remember_me_lifetime', 0);

        $db_lifetime = $should_remember
        ? $long_lifetime
        : $this->config->get('session.timeout');

        $session = $this->session_service->createNewSession($user, $db_lifetime);

        if (! $session) {
            $this->flash->error('Login failed. Please try again.');
            $this->firewall->fail()->capture();

            return false;
        }

        if ($should_remember) {
            $this->session->set('session.long_lived', true);
        } else {
            $this->session->delete('session.long_lived');
        }

        $this->session->regenerateId();
        $this->session->set($this->config->get('session.name'), $session->token);

        $this->firewall->clear()->capture();
        $this->flash->success('Welcome '.$user->name);

        $this->notifyLoginActivity($user);

        return true;
    }

    public function logout(?string $session_token = null): bool
    {
        $token = $session_token ?? $this->session->get($this->config->get('session.name'));
        $this->session->delete($this->config->get('session.name'));
        $this->session->destroy();

        if (empty($token)) {
            return true;
        }

        return $this->session_service->terminateSession($token);
    }

    public function isAuthenticated(): bool
    {
        return $this->user() !== null;
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

    private function notifyLoginActivity(User $user): void
    {
        $payload = Data::make($user->only(['id', 'name', 'email']));
        $data['browser'] = $this->agent->browser();
        $data['period'] = DateTimeHelper::now()->format('D, M d Y h:i A');

        Notify::inapp(LoginInAppNotification::class, $payload->add($data));

        defer(function () use ($data, $user) {
            activity('logged in to your account from a {browser} Browser as at {period}', $data, $user->id);
        });
    }
}
