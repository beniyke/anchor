<?php

declare(strict_types=1);

namespace App\Services\Auth\Interfaces;

use Helpers\Data\Contracts\DataTransferObject;

interface AuthServiceInterface
{
    public function isAuthenticated(): bool;

    public function viaGuard(string $guard): self;

    public function user(): ?object;

    public function login(DataTransferObject $request): bool;

    public function logout(): bool;

    public function logoutAll(): bool;

    public function isAuthorized(string $route): bool;

    public function getSessionKey(): ?string;
}
