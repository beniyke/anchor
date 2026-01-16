<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent post builder for the Scribe package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Services\Builders;

use DateTimeInterface;
use Helpers\String\Str;
use RuntimeException;
use Scribe\Models\Post;
use Scribe\Services\ScribeManagerService;

class PostBuilder
{
    private string $title = '';

    private ?string $slug = null;

    private string $content = '';

    private ?string $excerpt = null;

    private string $status = 'draft';

    private ?DateTimeInterface $publishedAt = null;

    private ?int $categoryId = null;

    private ?int $userId = null;

    private array $seoMeta = [];

    private array $tags = [];

    public function __construct(private readonly ScribeManagerService $manager)
    {
    }

    public function title(string $title): self
    {
        $this->title = $title;
        if ($this->slug === null) {
            $this->slug = Str::slug($title);
        }

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function excerpt(string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function publishedAt(DateTimeInterface $date): self
    {
        $this->publishedAt = $date;

        return $this;
    }

    public function category(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function author(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function seo(array $meta): self
    {
        $this->seoMeta = array_merge($this->seoMeta, $meta);

        return $this;
    }

    public function tags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function create(): Post
    {
        if (empty($this->title)) {
            throw new RuntimeException('Post title is required.');
        }

        $post = Post::create([
            'refid' => 'pst_' . Str::refid(),
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'published_at' => $this->publishedAt,
            'scribe_category_id' => $this->categoryId,
            'user_id' => $this->userId,
            'seo_meta' => $this->seoMeta,
        ]);

        if (!empty($this->tags)) {
            $post->tags()->sync($this->tags);
        }

        return $post;
    }
}
