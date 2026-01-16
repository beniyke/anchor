<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Collaboration Service
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Flow\Services;

use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Database\DB;
use Flow\Models\Attachment;
use Flow\Models\Comment;
use Flow\Models\Task;
use Flow\Services\Builders\AttachmentBuilder;
use Helpers\File\Adapters\Interfaces\FileManipulationInterface;
use Helpers\File\Adapters\Interfaces\FileMetaInterface;
use RuntimeException;

class CollaborationService
{
    public function __construct(
        protected ConfigServiceInterface $config,
        protected FileManipulationInterface $fileManipulation,
        protected FileMetaInterface $fileMeta
    ) {
    }

    public function makeAttachment(): AttachmentBuilder
    {
        return new AttachmentBuilder($this);
    }

    public function addComment(Task $task, User $user, string $content, array $mentions = []): Comment
    {
        $comment = new Comment();
        $comment->task_id = $task->id;
        $comment->user_id = $user->id;
        $comment->content = $content;
        $comment->mentions = $mentions;
        $comment->save();

        return $comment;
    }

    public function deleteComment(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function findCommentByRefid(string $refid): Comment
    {
        $comment = Comment::query()->where('refid', $refid)->first();
        if (!$comment) {
            throw new RuntimeException("Comment with refid [{$refid}] not found.");
        }

        return $comment;
    }

    public function attachFile(Task $task, User $user, array $fileData): Attachment
    {
        $attachment = new Attachment();
        $attachment->task_id = $task->id;
        $attachment->uploaded_by = $user->id;
        $attachment->fill($fileData); // path, filename, mime_type, size
        $attachment->save();

        return $attachment;
    }

    public function findAttachmentByRefid(string $refid): Attachment
    {
        $attachment = Attachment::query()->where('refid', $refid)->first();
        if (!$attachment) {
            throw new RuntimeException("Attachment with refid [{$refid}] not found.");
        }

        return $attachment;
    }

    public function removeAttachment(Attachment $attachment): bool
    {
        return DB::transaction(function () use ($attachment) {
            if ($attachment->path && $this->fileMeta->exists($attachment->path)) {
                if (!$this->fileManipulation->delete($attachment->path)) {
                    throw new RuntimeException("Failed to delete physical file: {$attachment->path}");
                }
            }

            return $attachment->delete();
        });
    }
}
