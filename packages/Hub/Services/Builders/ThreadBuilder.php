<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent thread builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services\Builders;

use Database\BaseModel;
use Hub\Models\Thread;
use Hub\Services\HubManagerService;

class ThreadBuilder
{
    private ?string $threadableType = null;

    private ?int $threadableId = null;

    private ?string $title = null;

    private bool $isPinned = false;

    private array $members = [];

    private array $metadata = [];

    private ?int $createdBy = null;

    public function __construct(
        private readonly HubManagerService $manager
    ) {
    }

    /**
     * Attach thread to a resource.
     */
    public function on(BaseModel $model): self
    {
        $this->threadableType = get_class($model);
        $this->threadableId = (int) $model->id;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set members (array of user IDs).
     */
    public function members(array $userIds): self
    {
        $this->members = $userIds;

        return $this;
    }

    public function addMember(int $userId): self
    {
        if (!in_array($userId, $this->members, true)) {
            $this->members[] = $userId;
        }

        return $this;
    }

    /**
     * Pin the thread.
     */
    public function pinned(): self
    {
        $this->isPinned = true;

        return $this;
    }

    public function metadata(array $data): self
    {
        $this->metadata = $data;

        return $this;
    }

    /**
     * Add metadata key.
     */
    public function with(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Set creator.
     */
    public function by(int $userId): self
    {
        $this->createdBy = $userId;

        return $this;
    }

    public function create(): Thread
    {
        return $this->manager->createThread([
            'threadable_type' => $this->threadableType,
            'threadable_id' => $this->threadableId,
            'title' => $this->title,
            'is_pinned' => $this->isPinned,
            'members' => $this->members,
            'metadata' => $this->metadata,
            'created_by' => $this->createdBy,
        ]);
    }
}
