<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ClientBuilder provides a fluent interface for creating client entities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client\Services\Builders;

use Client\Enums\ClientStatus;
use Client\Models\Client;
use Client\Services\ClientManagerService;

class ClientBuilder
{
    protected array $data = [];

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function email(string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function phone(string $phone): self
    {
        $this->data['phone'] = $phone;

        return $this;
    }

    public function status(ClientStatus|string $status): self
    {
        $this->data['status'] = $status;

        return $this;
    }

    public function reseller(int|string $ownerId): self
    {
        $this->data['owner_id'] = $ownerId;

        return $this;
    }

    public function user(int|string $userId): self
    {
        $this->data['user_id'] = $userId;

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->data['metadata'] = array_merge($this->data['metadata'] ?? [], $metadata);

        return $this;
    }

    public function create(): Client
    {
        return resolve(ClientManagerService::class)->create($this->data);
    }
}
