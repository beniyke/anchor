<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent message builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Hub\Services\Builders;

use Hub\Models\Message;
use Hub\Models\Thread;
use Hub\Services\HubManagerService;

class MessageBuilder
{
    private ?int $threadId = null;

    private ?int $parentId = null;

    private ?int $userId = null;

    private string $body = '';

    private bool $isPinned = false;

    private array $mentions = [];

    private array $metadata = [];

    public function __construct(
        private readonly HubManagerService $manager
    ) {
    }

    /**
     * Set the thread to post in.
     */
    public function in(Thread $thread): self
    {
        $this->threadId = $thread->id;

        return $this;
    }

    public function inThread(int $threadId): self
    {
        $this->threadId = $threadId;

        return $this;
    }

    /**
     * Set as reply to a message.
     */
    public function replyTo(Message $message): self
    {
        $this->parentId = $message->id;
        $this->threadId = $message->thread_id;

        return $this;
    }

    /**
     * Set the sender.
     */
    public function from(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set explicit mentions (user IDs).
     */
    public function mentions(array $userIds): self
    {
        $this->mentions = $userIds;

        return $this;
    }

    public function mention(int $userId): self
    {
        if (!in_array($userId, $this->mentions, true)) {
            $this->mentions[] = $userId;
        }

        return $this;
    }

    /**
     * Pin the message.
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

    public function send(): Message
    {
        return $this->manager->postMessage([
            'thread_id' => $this->threadId,
            'parent_id' => $this->parentId,
            'user_id' => $this->userId,
            'body' => $this->body,
            'is_pinned' => $this->isPinned,
            'mentions' => $this->mentions,
            'metadata' => $this->metadata,
        ]);
    }
}
