<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core service for feature flag management.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Services;

use App\Models\User;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use Core\Services\ConfigServiceInterface;
use Rollout\Models\Feature;
use Rollout\Services\Builders\FeatureBuilder;

class RolloutManagerService
{
    private array $cache = [];

    public function __construct(
        private readonly ConfigServiceInterface $config,
        private readonly AuthServiceInterface $auth
    ) {
    }

    public function isEnabled(string $featureSlug, ?User $user = null, array $context = []): bool
    {
        if (!$this->config->get('rollout.enabled', true)) {
            return $this->config->get('rollout.default_state', false);
        }

        $feature = $this->find($featureSlug);

        if (!$feature) {
            return $this->config->get('rollout.default_state', false);
        }

        // If no user provided, check if feature is globally enabled
        if (!$user) {
            $user = $this->auth->user();
        }

        return $feature->isEnabledFor($user, $context);
    }

    public function feature(): FeatureBuilder
    {
        return new FeatureBuilder($this);
    }

    public function enable(string $featureSlug): Feature
    {
        $feature = $this->findOrCreate($featureSlug);
        $feature->update([
            'is_enabled' => true,
            'percentage' => 100,
        ]);

        $this->clearCacheFor($featureSlug);

        return $feature;
    }

    public function disable(string $featureSlug): Feature
    {
        $feature = $this->findOrCreate($featureSlug);
        $feature->update([
            'is_enabled' => false,
            'percentage' => 0,
        ]);

        $this->clearCacheFor($featureSlug);

        return $feature;
    }

    public function setPercentage(string $featureSlug, int $percentage): Feature
    {
        $percentage = max(0, min(100, $percentage));

        $feature = $this->findOrCreate($featureSlug);
        $feature->update([
            'is_enabled' => $percentage > 0,
            'percentage' => $percentage,
        ]);

        $this->clearCacheFor($featureSlug);

        return $feature;
    }

    public function find(string $slug): ?Feature
    {
        if (isset($this->cache[$slug])) {
            return $this->cache[$slug];
        }

        $feature = Feature::findBySlug($slug);

        if ($feature && $this->config->get('rollout.cache.enabled', true)) {
            $this->cache[$slug] = $feature;
        }

        return $feature;
    }

    public function findOrCreate(string $slug, ?string $name = null): Feature
    {
        $feature = $this->find($slug);

        if (!$feature) {
            $feature = Feature::create([
                'slug' => $slug,
                'name' => $name ?? ucwords(str_replace(['-', '_'], ' ', $slug)),
                'is_enabled' => false,
                'percentage' => 0,
            ]);

            $this->cache[$slug] = $feature;
        }

        return $feature;
    }

    public function create(
        string $slug,
        string $name,
        ?string $description = null,
        bool $enabled = false,
        int $percentage = 0
    ): Feature {
        return Feature::create([
            'slug' => $slug,
            'name' => $name,
            'description' => $description,
            'is_enabled' => $enabled,
            'percentage' => $percentage,
        ]);
    }

    public function all(): array
    {
        return Feature::all()->toArray();
    }

    public function delete(string $slug): bool
    {
        $feature = $this->find($slug);

        if (!$feature) {
            return false;
        }

        $this->clearCacheFor($slug);

        return $feature->delete();
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }

    public function analytics(): RolloutAnalyticsService
    {
        return new RolloutAnalyticsService();
    }

    public function clearCacheFor(string $slug): void
    {
        unset($this->cache[$slug]);
    }

    public function getInRollout(): array
    {
        return Feature::where('is_enabled', true)
            ->where('percentage', '>', 0)
            ->where('percentage', '<', 100)
            ->get()
            ->toArray();
    }
}
