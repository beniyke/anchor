<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Facade for the Scribe (Blog) package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scribe;

use Scribe\Models\Category;
use Scribe\Models\Post;
use Scribe\Services\Builders\CategoryBuilder;
use Scribe\Services\Builders\PostBuilder;
use Scribe\Services\ScribeAnalyticsService;
use Scribe\Services\ScribeManagerService;

class Scribe
{
    /**
     * Start building a new post.
     */
    public static function post(): PostBuilder
    {
        return resolve(PostBuilder::class);
    }

    public static function analytics(): ScribeAnalyticsService
    {
        return resolve(ScribeAnalyticsService::class);
    }

    /**
     * Start building a new category.
     */
    public static function category(): CategoryBuilder
    {
        return resolve(CategoryBuilder::class);
    }

    /**
     * Record a view for a post.
     */
    public static function recordView(Post $post, ?int $userId = null, ?string $sessionId = null): void
    {
        resolve(ScribeManagerService::class)->recordView($post, $userId, $sessionId);
    }

    public static function findPost(string $slug): ?Post
    {
        return resolve(ScribeManagerService::class)->findPost($slug);
    }

    public static function findPostByRefId(string $refid): ?Post
    {
        return resolve(ScribeManagerService::class)->findPostByRefId($refid);
    }

    public static function findCategory(string $slug): ?Category
    {
        return resolve(ScribeManagerService::class)->findCategory($slug);
    }

    public static function findCategoryByRefId(string $refid): ?Category
    {
        return resolve(ScribeManagerService::class)->findCategoryByRefId($refid);
    }

    /**
     * Proxy calls to the ScribeManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(ScribeManagerService::class)->$method(...$arguments);
    }
}
