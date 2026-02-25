<?php

declare(strict_types=1);

namespace Academy\Services;

use Academy\Exceptions\AcademyException;
use Academy\Models\AcademyRating;

class RatingService
{
    /**
     * Submit a rating for a program.
     */
    public function submit(int $userId, int $programId, int $rating, ?string $review = null): AcademyRating
    {
        if ($rating < 1 || $rating > 5) {
            throw new AcademyException("Rating must be between 1 and 5.");
        }

        return AcademyRating::updateOrCreate(
            ['user_id' => $userId, 'program_id' => $programId],
            [
                'rating' => $rating,
                'review' => $review,
            ]
        );
    }

    public function getAverageRating(int $programId): float
    {
        return (float) AcademyRating::where('program_id', $programId)->avg('rating') ?: 0.0;
    }

    public function getFeaturedReviews(int $programId, int $limit = 5): array
    {
        return AcademyRating::where('program_id', $programId)
            ->where('is_featured', true)
            ->with('user')
            ->limit($limit)
            ->get();
    }
}
