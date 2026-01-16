<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Subscriber Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Services\Builders;

use Blish\Models\Subscriber;
use Blish\Services\BlishManagerService;

class SubscriberBuilder
{
    protected string $email;

    protected ?string $name = null;

    protected array $data = [];

    protected array $lists = [];

    protected array $tags = [];

    protected string $status = 'pending';

    public function __construct(
        protected BlishManagerService $manager
    ) {
    }

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function name(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function active(): self
    {
        $this->status = 'active';

        return $this;
    }

    public function save(): Subscriber
    {
        $data = $this->data;
        $name = $this->name ?? $data['name'] ?? null;
        $status = $this->status ?? $data['status'] ?? 'pending';

        unset($data['name'], $data['status']);

        $subscriber = $this->manager->persistSubscriber([
            'email' => $this->email,
            'name' => $name,
            'status' => $status,
            'metadata' => $data,
        ]);

        if (!empty($this->lists)) {
            $subscriber->lists()->sync($this->lists);
        }

        if (!empty($this->tags)) {
            $subscriber->tags()->sync($this->tags);
        }

        return $subscriber;
    }
}
