<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Testimonial Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Proof\Services\Builders;

use Proof\Models\Source;
use Proof\Models\Testimonial;
use Proof\Services\ProofManagerService;

class TestimonialBuilder
{
    private array $data = [];

    private ?Testimonial $instance = null;

    public function setInstance(?Testimonial $testimonial): self
    {
        $this->instance = $testimonial;

        return $this;
    }

    public function source(Source|int $source): self
    {
        $this->data['proof_source_id'] = $source instanceof Source ? $source->id : $source;

        return $this;
    }

    public function content(string $content): self
    {
        $this->data['content'] = $content;

        return $this;
    }

    public function rating(int $rating): self
    {
        $this->data['rating'] = $rating;

        return $this;
    }

    public function status(string $status): self
    {
        $this->data['status'] = $status;

        return $this;
    }

    public function video(string $url): self
    {
        $this->data['video_url'] = $url;

        return $this;
    }

    public function featured(bool $featured = true): self
    {
        $this->data['is_featured'] = $featured;

        return $this;
    }

    public function verified(bool $verified = true): self
    {
        $this->data['is_verified'] = $verified;

        return $this;
    }

    public function save(): Testimonial
    {
        return resolve(ProofManagerService::class)->saveTestimonial($this->data, $this->instance);
    }
}
