<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Post Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services\Builders;

use App\Models\User;
use Pulse\Models\Post;
use Pulse\Models\Thread;
use Pulse\Services\PulseManagerService;
use RuntimeException;

class PostBuilder
{
    protected PulseManagerService $manager;

    protected ?User $user = null;

    protected ?Thread $thread = null;

    protected string $content = '';

    protected ?int $parentId = null;

    public function __construct(PulseManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function by(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function on(Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function replyingTo(int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function create(): Post
    {
        if (!$this->user || !$this->thread || empty($this->content)) {
            throw new RuntimeException("Post requires a user, thread, and content.");
        }

        return $this->manager->createPost($this->user, $this->thread, $this->content, $this->parentId);
    }
}
