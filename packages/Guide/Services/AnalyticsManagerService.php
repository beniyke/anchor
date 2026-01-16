<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Analytics Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Services;

use Audit\Audit;
use Guide\Models\Article;
use Guide\Models\Feedback;
use Guide\Models\SearchLog;

class AnalyticsManagerService
{
    /**
     * Log a search query.
     */
    public function logSearch(string $query, int $resultsCount, ?int $userId = null, array $metadata = []): void
    {
        SearchLog::create([
            'query' => $query,
            'results_count' => $resultsCount,
            'user_id' => $userId,
            'ip_address' => $metadata['ip'] ?? null,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Record an article view.
     */
    public function recordView(Article $article): void
    {
        $article->increment('view_count');

        if (class_exists('Audit\Audit')) {
            Audit::log('guide.article.viewed', [
                'id' => $article->id,
                'title' => $article->title,
            ], $article);
        }
    }

    /**
     * Submit feedback for an article.
     */
    public function submitFeedback(Article $article, int $rating, ?string $comment = null, ?int $userId = null, array $metadata = []): Feedback
    {
        $feedback = Feedback::create([
            'guide_article_id' => $article->id,
            'rating' => $rating,
            'comment' => $comment,
            'user_id' => $userId,
            'ip_address' => $metadata['ip'] ?? null,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('guide.article.feedback', [
                'article_id' => $article->id,
                'rating' => $rating,
            ], $article);
        }

        return $feedback;
    }

    public function getPopularArticles(int $limit = 5)
    {
        return Article::where('status', 'published')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
