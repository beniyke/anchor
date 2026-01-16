<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Category Builder for the Scribe package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe\Services\Builders;

use Helpers\String\Str;
use Scribe\Models\Category;
use Scribe\Services\ScribeManagerService;

class CategoryBuilder
{
    protected array $data = [];

    public function __construct(private readonly ScribeManagerService $manager)
    {
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        if (empty($this->data['slug'])) {
            $this->data['slug'] = Str::slug($name);
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

    public function create(): Category
    {
        $this->data['refid'] = $this->data['refid'] ?? 'cat_' . Str::refid();

        return resolve(ScribeManagerService::class)->createCategory($this->data);
    }
}
