<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Thread Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services\Builders;

use App\Models\User;
use Pulse\Models\Channel;
use Pulse\Models\Thread;
use Pulse\Services\PulseManagerService;
use RuntimeException;

class ThreadBuilder
{
    protected PulseManagerService $manager;

    protected ?User $user = null;

    protected ?Channel $channel = null;

    protected string $title = '';

    protected string $content = '';

    protected bool $pinned = false;

    protected bool $locked = false;

    public function __construct(PulseManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function by(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function in(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function pinned(bool $pinned = true): self
    {
        $this->pinned = $pinned;

        return $this;
    }

    public function locked(bool $locked = true): self
    {
        $this->locked = $locked;

        return $this;
    }

    public function create(): Thread
    {
        if (!$this->user || !$this->channel || empty($this->title)) {
            throw new RuntimeException("Thread requires a user, channel, and title.");
        }

        $thread = $this->manager->createThread($this->user, $this->channel, $this->title, $this->content);

        if ($this->pinned || $this->locked) {
            $thread->update([
                'is_pinned' => $this->pinned,
                'is_locked' => $this->locked,
            ]);
        }

        return $thread;
    }
}
