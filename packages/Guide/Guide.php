<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Guide.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide;

use Database\Collections\ModelCollection;
use Guide\Models\Article;
use Guide\Models\Category;
use Guide\Services\AnalyticsManagerService;
use Guide\Services\Builders\ArticleBuilder;
use Guide\Services\Builders\CategoryBuilder;
use Guide\Services\GuideManagerService;

class Guide
{
    /**
     * Start a fluent article generation builder.
     */
    public static function article(): ArticleBuilder
    {
        return resolve(GuideManagerService::class)->article();
    }

    /**
     * Start a fluent category generation builder.
     */
    public static function category(): CategoryBuilder
    {
        return resolve(GuideManagerService::class)->category();
    }

    public static function findArticle(string $slug): ?Article
    {
        return resolve(GuideManagerService::class)->findArticle($slug);
    }

    public static function findCategory(string $slug): ?Category
    {
        return resolve(GuideManagerService::class)->findCategory($slug);
    }

    public static function findCategoryByRefId(string $refid): ?Category
    {
        return resolve(GuideManagerService::class)->findCategoryByRefId($refid);
    }

    public static function findArticleByRefId(string $refid): ?Article
    {
        return resolve(GuideManagerService::class)->findArticleByRefId($refid);
    }

    public static function search(string $query, array $filters = [], array $metadata = []): ModelCollection
    {
        return resolve(GuideManagerService::class)->search($query, $filters, $metadata);
    }

    public static function analytics(): AnalyticsManagerService
    {
        return resolve(AnalyticsManagerService::class);
    }

    /**
     * Forward static calls to GuideManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(GuideManagerService::class)->$method(...$arguments);
    }
}
