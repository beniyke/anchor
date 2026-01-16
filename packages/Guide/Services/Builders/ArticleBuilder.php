<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Article Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Services\Builders;

use Guide\Models\Article;
use Guide\Models\Category;
use Guide\Services\GuideManagerService;
use Helpers\String\Str;

class ArticleBuilder
{
    protected array $data = [];

    public function refid(string $refid): self
    {
        $this->data['refid'] = $refid;

        return $this;
    }

    public function title(string $title): self
    {
        $this->data['title'] = $title;

        if (empty($this->data['slug'])) {
            $this->data['slug'] = strtolower(str_replace(' ', '-', $title));
        }

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->data['slug'] = $slug;

        return $this;
    }

    public function content(string $content): self
    {
        $this->data['content'] = $content;

        return $this;
    }

    public function category(Category|int $category): self
    {
        $this->data['guide_category_id'] = $category instanceof Category ? $category->id : $category;

        return $this;
    }

    public function status(string $status): self
    {
        $this->data['status'] = $status;

        return $this;
    }

    public function featured(bool $featured = true): self
    {
        $this->data['is_featured'] = $featured;

        return $this;
    }

    public function create(): Article
    {
        $this->data['refid'] = $this->data['refid'] ?? 'art_' . Str::refid();

        return resolve(GuideManagerService::class)->createArticle($this->data);
    }
}
