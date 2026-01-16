<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Performance Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services;

use App\Models\User;
use Audit\Audit;
use DateTimeInterface;
use Hub\Hub;
use Metric\Models\Feedback;
use Metric\Models\Recognition;
use Metric\Models\Review;
use Metric\Models\ReviewCycle;

class PerformanceManagerService
{
    /**
     * Start a new review cycle.
     */
    public function startCycle(string $name, DateTimeInterface $start, DateTimeInterface $end): ReviewCycle
    {
        return ReviewCycle::create([
            'name' => $name,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'active',
        ]);
    }

    /**
     * Submit feedback for a user.
     */
    public function giveFeedback(User $author, User $recipient, string $content, ?Review $review = null): Feedback
    {
        $feedback = Feedback::create([
            'metric_review_id' => $review?->id,
            'user_id' => $recipient->id,
            'author_id' => $author->id,
            'content' => $content,
        ]);

        if (class_exists('Hub\Hub')) {
            Hub::thread()
                ->title("Performance Feedback for {$recipient->name}")
                ->content($content)
                ->create();
        }

        return $feedback;
    }

    public function recognize(User $sender, User $receiver, string $awardType, string $message): Recognition
    {
        $recognition = Recognition::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'award_type' => $awardType,
            'message' => $message,
        ]);

        if (class_exists('Audit\Audit')) {
            Audit::log('metric.recognition.sent', [
                'from' => $sender->email,
                'to' => $receiver->email,
                'type' => $awardType
            ], $recognition);
        }

        return $recognition;
    }
}
