<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Category Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Services\Builders;

use Guide\Models\Category;
use Guide\Services\GuideManagerService;
use Helpers\String\Str;

class CategoryBuilder
{
    protected array $data = [];

    public function refid(string $refid): self
    {
        $this->data['refid'] = $refid;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        if (empty($this->data['slug'])) {
            $this->data['slug'] = strtolower(str_replace(' ', '-', $name));
        }

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->data['slug'] = $slug;

        return $this;
    }

    public function description(string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function parent(Category|int $parent): self
    {
        $this->data['parent_id'] = $parent instanceof Category ? $parent->id : $parent;

        return $this;
    }

    public function order(int $order): self
    {
        $this->data['order'] = $order;

        return $this;
    }

    public function create(): Category
    {
        $this->data['refid'] = $this->data['refid'] ?? 'cat_' . Str::refid();

        return resolve(GuideManagerService::class)->createCategory($this->data);
    }
}
