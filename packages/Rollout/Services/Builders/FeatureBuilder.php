<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent builder for creating features.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Services\Builders;

use Carbon\Carbon;
use InvalidArgumentException;
use Rollout\Enums\SegmentType;
use Rollout\Models\Feature;
use Rollout\Models\Segment;
use Rollout\Services\RolloutManagerService;

class FeatureBuilder
{
    private ?string $slug = null;

    private ?string $name = null;

    private ?string $description = null;

    private bool $enabled = false;

    private int $percentage = 0;

    private ?Carbon $startsAt = null;

    private ?Carbon $endsAt = null;

    private array $userIds = [];

    private array $roles = [];

    private array $plans = [];

    private array $emailDomains = [];

    public function __construct(
        private readonly RolloutManagerService $manager
    ) {
    }

    public function slug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function percentage(int $percentage): self
    {
        $this->percentage = max(0, min(100, $percentage));
        $this->enabled = $this->percentage > 0;

        return $this;
    }

    public function startsAt(string|Carbon $date): self
    {
        $this->startsAt = is_string($date) ? Carbon::parse($date) : $date;

        return $this;
    }

    public function endsAt(string|Carbon $date): self
    {
        $this->endsAt = is_string($date) ? Carbon::parse($date) : $date;

        return $this;
    }

    public function forUsers(array $userIds): self
    {
        $this->userIds = array_merge($this->userIds, $userIds);

        return $this;
    }

    public function forRoles(array $roles): self
    {
        $this->roles = array_merge($this->roles, $roles);

        return $this;
    }

    public function forPlans(array $plans): self
    {
        $this->plans = array_merge($this->plans, $plans);

        return $this;
    }

    public function forDomains(array $domains): self
    {
        $this->emailDomains = array_merge($this->emailDomains, $domains);

        return $this;
    }

    public function create(?string $slug = null, ?string $name = null): Feature
    {
        $slug = $slug ?? $this->slug;
        $name = $name ?? $this->name ?? ucwords(str_replace(['-', '_'], ' ', $slug));

        if (!$slug) {
            throw new InvalidArgumentException('Feature slug is required.');
        }

        $feature = Feature::create([
            'slug' => $slug,
            'name' => $name,
            'description' => $this->description,
            'is_enabled' => $this->enabled,
            'percentage' => $this->percentage ?: ($this->enabled ? 100 : 0),
            'starts_at' => $this->startsAt,
            'ends_at' => $this->endsAt,
        ]);

        $this->createSegments($feature);

        return $feature;
    }

    private function createSegments(Feature $feature): void
    {
        if (!empty($this->userIds)) {
            $segment = Segment::create([
                'feature_id' => $feature->id,
                'name' => 'Beta Users',
                'type' => SegmentType::USER_ID,
            ]);

            foreach ($this->userIds as $userId) {
                $segment->users()->create(['user_id' => $userId]);
            }
        }

        foreach ($this->roles as $role) {
            Segment::create([
                'feature_id' => $feature->id,
                'name' => "Role: {$role}",
                'type' => SegmentType::ROLE,
                'value' => $role,
            ]);
        }

        foreach ($this->plans as $plan) {
            Segment::create([
                'feature_id' => $feature->id,
                'name' => "Plan: {$plan}",
                'type' => SegmentType::PLAN,
                'value' => $plan,
            ]);
        }

        foreach ($this->emailDomains as $domain) {
            Segment::create([
                'feature_id' => $feature->id,
                'name' => "Domain: {$domain}",
                'type' => SegmentType::EMAIL_DOMAIN,
                'value' => $domain,
            ]);
        }
    }
}
