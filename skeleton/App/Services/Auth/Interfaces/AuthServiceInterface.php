<?php

declare(strict_types=1);

namespace App\Services\Auth\Interfaces;

use App\Requests\LoginRequest;

interface AuthServiceInterface
{
    public function isAuthenticated(): bool;

    public function user(): ?object;

    public function login(LoginRequest $request): bool;

    public function logout(?string $session_token = null): bool;

    public function isAuthorized(string $route): bool;
}
