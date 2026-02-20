<?php

declare(strict_types=1);

namespace Rank\Services;

use Helpers\Http\Request;
use Helpers\String\Str;

class SeoManager
{
    protected array $meta = [];

    protected array $og = [];

    protected array $twitter = [];

    protected array $schema = [];

    protected ?Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request;
        $this->loadDefaults();
    }

    protected function loadDefaults(): void
    {
        if ($this->request) {
            $entity = $this->request->getRouteContext('entity');
            $action = $this->request->getRouteContext('action');

            if ($entity) {
                $title = Str::title($entity);
                if ($action) {
                    $title = Str::title($action) . ' ' . $title;
                }
                $this->setTitle($title);
            }
        }
    }

    public function setTitle(string $title): self
    {
        $this->meta['title'] = $title;
        $this->og['og:title'] = $title;
        $this->twitter['twitter:title'] = $title;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->meta['description'] = $description;
        $this->og['og:description'] = $description;
        $this->twitter['twitter:description'] = $description;

        return $this;
    }

    public function setCanonical(string $url): self
    {
        $this->meta['canonical'] = $url;
        $this->og['og:url'] = $url;

        return $this;
    }

    public function setImage(string $url): self
    {
        $this->og['og:image'] = $url;
        $this->twitter['twitter:image'] = $url;

        return $this;
    }

    public function setMeta(string $name, string $content): self
    {
        $this->meta[$name] = $content;

        return $this;
    }

    public function setOg(string $property, string $content): self
    {
        $this->og[$property] = $content;

        return $this;
    }

    public function render(): string
    {
        $html = [];

        // Title
        if (isset($this->meta['title'])) {
            $html[] = "<title>{$this->meta['title']}</title>";
        }

        // Canonical
        if (isset($this->meta['canonical'])) {
            $html[] = '<link rel="canonical" href="' . $this->meta['canonical'] . '">';
        }

        // General Meta
        foreach ($this->meta as $name => $content) {
            if (in_array($name, ['title', 'canonical'])) {
                continue;
            }
            $html[] = '<meta name="' . $name . '" content="' . $content . '">';
        }

        // OG Meta
        foreach ($this->og as $property => $content) {
            $html[] = '<meta property="' . $property . '" content="' . $content . '">';
        }

        // Twitter Meta
        foreach ($this->twitter as $name => $content) {
            $html[] = '<meta name="' . $name . '" content="' . $content . '">';
        }

        return implode("\n", $html);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
