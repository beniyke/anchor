<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Feedback.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $guide_article_id
 * @property int             $rating
 * @property ?string         $comment
 * @property ?string         $ip_address
 * @property ?int            $user_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Article $article
 */
class Feedback extends BaseModel
{
    protected string $table = 'guide_feedback';

    protected array $fillable = [
        'guide_article_id',
        'rating',
        'comment',
        'ip_address',
        'user_id',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'guide_article_id');
    }
}
