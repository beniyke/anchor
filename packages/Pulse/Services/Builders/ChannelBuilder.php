<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Channel Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services\Builders;

use Pulse\Models\Channel;
use Pulse\Services\PulseManagerService;
use RuntimeException;

class ChannelBuilder
{
    protected PulseManagerService $manager;

    protected string $name = '';

    protected ?string $slug = null;

    protected ?string $description = null;

    protected ?int $parentId = null;

    protected int $order = 0;

    protected bool $private = false;

    public function __construct(PulseManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function parent(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function order(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function private(bool $private = true): self
    {
        $this->private = $private;

        return $this;
    }

    public function create(): Channel
    {
        if (empty($this->name)) {
            throw new RuntimeException("Channel name is required.");
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?? $this->manager->generateSlug($this->name, Channel::class),
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'order' => $this->order,
            'is_private' => $this->private,
        ];

        return $this->manager->createChannel($data);
    }
}
