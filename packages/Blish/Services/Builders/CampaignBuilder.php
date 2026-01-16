<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Campaign Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Services\Builders;

use Blish\Models\Campaign;
use Blish\Services\BlishManagerService;
use DateTimeInterface;
use Helpers\String\Str;

class CampaignBuilder
{
    protected string $title;

    protected string $subject;

    protected ?int $templateId = null;

    protected array $metadata = [];

    protected ?string $scheduledAt = null;

    public function __construct(
        protected BlishManagerService $manager
    ) {
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function template(int $templateId): self
    {
        $this->templateId = $templateId;

        return $this;
    }

    public function meta(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function schedule($scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt instanceof DateTimeInterface
            ? $scheduledAt->format('Y-m-d H:i:s')
            : $scheduledAt;

        return $this;
    }

    public function create(): Campaign
    {
        return Campaign::create([
            'refid' => Str::random('alnum', 32),
            'title' => $this->title,
            'subject' => $this->subject,
            'template_id' => $this->templateId,
            'status' => isset($this->scheduledAt) ? 'scheduled' : 'draft',
            'scheduled_at' => $this->scheduledAt ?? null,
            'metadata' => $this->metadata,
        ]);
    }
}
