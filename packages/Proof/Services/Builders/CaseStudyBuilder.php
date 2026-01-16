<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Case Study Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Services\Builders;

use Proof\Models\CaseStudy;
use Proof\Models\Source;
use Proof\Services\ProofManagerService;

class CaseStudyBuilder
{
    private array $data = [];

    private ?CaseStudy $instance = null;

    public function setInstance(?CaseStudy $caseStudy): self
    {
        $this->instance = $caseStudy;

        return $this;
    }

    public function source(Source|int $source): self
    {
        $this->data['proof_source_id'] = $source instanceof Source ? $source->id : $source;

        return $this;
    }

    public function title(string $title): self
    {
        $this->data['title'] = $title;

        return $this;
    }

    public function slug(string $slug): self
    {
        $this->data['slug'] = $slug;

        return $this;
    }

    public function summary(string $summary): self
    {
        $this->data['summary'] = $summary;

        return $this;
    }

    public function status(string $status): self
    {
        $this->data['status'] = $status;

        return $this;
    }

    public function image(string $url): self
    {
        $this->data['featured_image'] = $url;

        return $this;
    }

    public function save(): CaseStudy
    {
        return resolve(ProofManagerService::class)->saveCaseStudy($this->data, $this->instance);
    }
}
