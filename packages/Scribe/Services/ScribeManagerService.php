<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core service for the Scribe (Blog) package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Services;

use Audit\Audit;
use DateTimeInterface;
use Helpers\DateTimeHelper;
use InvalidArgumentException;
use Scribe\Models\Category;
use Scribe\Models\Comment;
use Scribe\Models\Event;
use Scribe\Models\Post;

class ScribeManagerService
{
    public function publish(Post $post): bool
    {
        $post->update([
            'status' => 'published',
            'published_at' => DateTimeHelper::now(),
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('scribe.post.published', ['title' => $post->title], $post);
        }

        return true;
    }

    /**
     * Schedule a post for future publication.
     */
    public function schedule(Post $post, DateTimeInterface $publishAt): bool
    {
        $post->update([
            'status' => 'scheduled',
            'published_at' => $publishAt,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('scribe.post.scheduled', [
                'title' => $post->title,
                'publish_at' => $publishAt->format('Y-m-d H:i:s'),
            ], $post);
        }

        return true;
    }

    /**
     * Record a post view.
     */
    public function recordView(Post $post, ?int $userId = null, ?string $sessionId = null): void
    {
        Event::create([
            'scribe_post_id' => $post->id,
            'event_type' => 'view',
            'user_id' => $userId,
            'session_id' => $sessionId,
        ]);
    }

    public function addComment(Post $post, array $data, ?int $userId = null): Comment
    {
        if (empty($data['content'])) {
            throw new InvalidArgumentException('Comment content cannot be empty.');
        }

        $comment = Comment::create([
            'refid' => uniqid('cmt_', true),
            'scribe_post_id' => $post->id,
            'user_id' => $userId,
            'content' => $data['content'],
            'status' => $userId ? 'approved' : 'pending', // Auto-approve logged-in users? Logic can be adjusted.
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('scribe.comment.added', [
                'post_title' => $post->title,
                'comment_refid' => $comment->refid,
            ], $post);
        }

        return $comment;
    }

    /**
     * Generate SEO metadata for a post if not manually set.
     */
    public function generateSeoMeta(Post $post): array
    {
        $meta = $post->seo_meta ?? [];

        if (empty($meta['title'])) {
            $meta['title'] = $post->title;
        }

        if (empty($meta['description'])) {
            // Use excerpt or first 160 chars of content
            $content = $post->excerpt ?: strip_tags($post->content);
            $meta['description'] = mb_substr($content, 0, 160);
        }

        return $meta;
    }

    /**
     * Clear post cache and refresh indexes.
     */
    public function clearCache(): int
    {
        $cache = cache('scribe');
        $count = count($cache->keys());

        $cache->clear();

        if (class_exists('Audit\Audit')) {
            Audit::log('scribe.cache.cleared', ['keys_count' => $count]);
        }

        return $count;
    }

    /**
     * Internal: Create a category from data.
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function findPost(string $slug): ?Post
    {
        return Post::where('slug', $slug)->first();
    }

    public function findPostByRefId(string $refid): ?Post
    {
        return Post::where('refid', $refid)->first();
    }

    public function findCategory(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function findCategoryByRefId(string $refid): ?Category
    {
        return Category::where('refid', $refid)->first();
    }
}
