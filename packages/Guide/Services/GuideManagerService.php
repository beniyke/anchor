<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Guide Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Services;

use Audit\Audit;
use Database\Collections\ModelCollection;
use Guide\Models\Article;
use Guide\Models\Category;
use Guide\Services\Builders\ArticleBuilder;
use Guide\Services\Builders\CategoryBuilder;
use Helpers\DateTimeHelper;

class GuideManagerService
{
    /**
     * Start a fluent article generation builder.
     */
    public function article(): ArticleBuilder
    {
        return resolve(ArticleBuilder::class);
    }

    /**
     * Start a fluent category generation builder.
     */
    public function category(): CategoryBuilder
    {
        return resolve(CategoryBuilder::class);
    }

    public function findArticle(string $slug): ?Article
    {
        return Article::where('slug', $slug)->first();
    }

    public function findCategory(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    public function findCategoryByRefId(string $refid): ?Category
    {
        return Category::where('refid', $refid)->first();
    }

    public function findArticleByRefId(string $refid): ?Article
    {
        return Article::where('refid', $refid)->first();
    }

    public function getPublishedArticles(): ModelCollection
    {
        return Article::where('status', 'published')->get();
    }

    public function getRootCategories(): ModelCollection
    {
        return Category::where('parent_id', null)->orderBy('order', 'asc')->get();
    }

    /**
     * Search articles by query with status and category filtering.
     */
    public function search(string $query, array $filters = [], array $metadata = []): ModelCollection
    {
        $limit = config('guide.search.limit', 10);

        $queryBuilder = Article::where('status', 'published')
            ->where(function ($q) use ($query) {
                // Search in title and content
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            });

        if (!empty($filters['category'])) {
            $queryBuilder->where('guide_category_id', $filters['category']);
        }

        $articles = $queryBuilder->limit($limit)->get();

        // Track search analytics
        if (config('guide.search.log_enabled', true)) {
            resolve(AnalyticsManagerService::class)->logSearch($query, $articles->count(), null, $metadata);
        }

        return $articles;
    }

    /**
     * Attach media to an article.
     */
    public function attachMedia(Article $article, int $mediaId, string $type = 'attachment'): void
    {
        // Pivot guide_article_media handlings
        $article->db()->table('guide_article_media')->insert([
            'guide_article_id' => $article->id,
            'media_id' => $mediaId,
            'type' => $type,
            'created_at' => DateTimeHelper::now(),
            'updated_at' => DateTimeHelper::now(),
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('guide.article.media_attached', [
                'article_id' => $article->id,
                'media_id' => $mediaId,
                'type' => $type,
            ], $article);
        }
    }

    /**
     * Relate two articles.
     */
    public function relateArticles(Article $article, Article $related): void
    {
        $article->relatedArticles()->attach($related->id);
    }

    /**
     * Internal: Create an article from data.
     */
    public function createArticle(array $data): Article
    {
        return Article::create($data);
    }

    /**
     * Internal: Create a category from data.
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }
}
