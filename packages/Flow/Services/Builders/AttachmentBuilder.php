<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Attachment Builder
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services\Builders;

use App\Models\User;
use Flow\Models\Attachment;
use Flow\Models\Task;
use Flow\Services\CollaborationService;
use RuntimeException;

class AttachmentBuilder
{
    protected array $data = [];

    protected ?Task $task = null;

    protected ?User $user = null;

    public function __construct(protected CollaborationService $service)
    {
    }

    public function to(Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function by(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function path(string $path): self
    {
        $this->data['path'] = $path;

        return $this;
    }

    public function filename(string $filename): self
    {
        $this->data['filename'] = $filename;

        return $this;
    }

    public function mime(string $mime): self
    {
        $this->data['mime_type'] = $mime;

        return $this;
    }

    public function size(int $bytes): self
    {
        $this->data['size'] = $bytes;

        return $this;
    }

    public function save(): Attachment
    {
        if (!$this->task) {
            throw new RuntimeException("Task is required for an attachment.");
        }

        if (!$this->user) {
            throw new RuntimeException("User is required for an attachment.");
        }

        return $this->service->attachFile(
            $this->task,
            $this->user,
            $this->data
        );
    }
}
