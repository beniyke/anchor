<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent ticket category builder.
 */

namespace Support\Services\Builders;

use Support\Models\TicketCategory;

class CategoryBuilder
{
    private string $name = '';

    private ?string $slug = null;

    private ?string $description = null;

    private bool $isActive = true;

    private int $displayOrder = 0;

    public function name(string $name): self
    {
        $this->name = $name;

        if ($this->slug === null) {
            $this->slug = strtolower(str_replace(' ', '-', $name));
        }

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function active(bool $active = true): self
    {
        $this->isActive = $active;

        return $this;
    }

    public function inactive(): self
    {
        $this->isActive = false;

        return $this;
    }

    public function order(int $order): self
    {
        $this->displayOrder = $order;

        return $this;
    }

    public function create(): TicketCategory
    {
        return TicketCategory::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'display_order' => $this->displayOrder,
        ]);
    }
}
